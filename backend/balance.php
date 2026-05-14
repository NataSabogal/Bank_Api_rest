<?php
require_once __DIR__ . '/config.php';

$personId = $_GET['person_id'] ?? '';
if (empty($personId)) {
    jsonResponse(['error' => 'person_id requerido'], 400);
}

$account = getBankData($personId);
if (!$account) {
    jsonResponse(['error' => 'Cuenta no encontrada'], 404);
}

try {
    $db = getDB();

    $stmt = $db->prepare('SELECT COALESCE(SUM(monto), 0) as total_gastado FROM compra WHERE person_id = :pid');
    $stmt->execute(['pid' => $personId]);
    $totalGastado = (float) $stmt->fetchColumn();

    $stmt = $db->prepare('SELECT COALESCE(SUM(cantidad), 0) as total FROM tamalbit WHERE person_id = :pid');
    $stmt->execute(['pid' => $personId]);
    $totalTamalbits = (int) $stmt->fetchColumn();

    jsonResponse([
        'balance' => (float) $account['balance'],
        'name' => $account['name'],
        'personId' => $account['personId'],
        'totalGastado' => $totalGastado,
        'totalTamalbits' => $totalTamalbits,
        'consistente' => abs((10000 - $totalGastado) - $account['balance']) < 0.01,
    ]);
} catch (Exception $e) {
    jsonResponse(['error' => 'Error de servidor'], 500);
}
