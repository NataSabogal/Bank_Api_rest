<?php
require_once __DIR__ . '/config.php';

$personId = $_GET['person_id'] ?? '';
if (empty($personId)) {
    jsonResponse(['error' => 'person_id requerido'], 400);
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'SELECT t.*, c.descripcion as compra_desc, c.monto
         FROM tamalbit t
         LEFT JOIN compra c ON t.origen_compra_id = c.id_compra
         WHERE t.person_id = :pid
         ORDER BY t.fecha_creacion DESC'
    );
    $stmt->execute(['pid' => $personId]);
    $tamalbits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare('SELECT COALESCE(SUM(cantidad), 0) as total FROM tamalbit WHERE person_id = :pid');
    $stmt->execute(['pid' => $personId]);
    $total = (int) $stmt->fetchColumn();

    jsonResponse(['total' => $total, 'historial' => $tamalbits]);
} catch (Exception $e) {
    jsonResponse(['error' => 'Error de servidor'], 500);
}
