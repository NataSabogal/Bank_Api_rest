<?php
require_once __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$personId = trim($input['person_id'] ?? '');
$items = $input['items'] ?? [];

if (empty($personId) || empty($items)) {
    jsonResponse(['success' => false, 'error' => 'Datos incompletos'], 400);
}

$total = 0;
foreach ($items as $item) {
    $total += (float) $item['precio'] * (int) $item['cantidad'];
}

if ($total <= 0) {
    jsonResponse(['success' => false, 'error' => 'Total invalido'], 400);
}

$account = getBankData($personId);
if (!$account) {
    jsonResponse(['success' => false, 'error' => 'Cuenta no encontrada'], 404);
}

if ($account['balance'] < $total) {
    jsonResponse(['success' => false, 'error' => 'Saldo insuficiente'], 400);
}

$deductResult = deductFromBank($personId, $total, 'Compra en CarniBank');
if (!$deductResult) {
    jsonResponse(['success' => false, 'error' => 'Error al procesar el pago'], 500);
}

$newBalance = $deductResult['newBalance'];

try {
    $db = getDB();
    $db->beginTransaction();

    $totalTamalbits = 0;
    $stmt = $db->prepare(
        'INSERT INTO compra (person_id, id_producto, cantidad, monto, descripcion, saldo_antes, saldo_despues, tamalbits_obtenidos)
         VALUES (:pid, :idp, :cant, :monto, :desc, :s_antes, :s_despues, :tb)'
    );

    foreach ($items as $item) {
        $cantidad = (int) $item['cantidad'];
        $precio = (float) $item['precio'];
        $monto = $precio * $cantidad;

        $tamalbits = 0;
        if ((int) $item['id_producto'] === 1) {
            $tamalbits = (int) ($monto / 10);
        }

        $stmt->execute([
            'pid' => $personId,
            'idp' => (int) $item['id_producto'],
            'cant' => $cantidad,
            'monto' => $monto,
            'desc' => $item['nombre'] ?? '',
            's_antes' => $account['balance'],
            's_despues' => $newBalance,
            'tb' => $tamalbits,
        ]);

        if ($tamalbits > 0) {
            $compraId = $db->lastInsertId();
            $stmt2 = $db->prepare(
                'INSERT INTO tamalbit (person_id, cantidad, origen_compra_id) VALUES (:pid, :cant, :cid)'
            );
            $stmt2->execute(['pid' => $personId, 'cant' => $tamalbits, 'cid' => $compraId]);
            $totalTamalbits += $tamalbits;
        }
    }

    $db->commit();

    jsonResponse([
        'success' => true,
        'newBalance' => $newBalance,
        'totalDeducted' => $total,
        'tamalbitsGanados' => $totalTamalbits,
        'message' => 'Compra realizada con exito',
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonResponse(['success' => false, 'error' => 'Error al guardar la compra'], 500);
}
