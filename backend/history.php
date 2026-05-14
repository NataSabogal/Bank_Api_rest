<?php
require_once __DIR__ . '/config.php';

$personId = $_GET['person_id'] ?? '';
if (empty($personId)) {
    jsonResponse(['error' => 'person_id requerido'], 400);
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'SELECT c.*, p.nombre as producto_nombre, p.imagen_url
         FROM compra c
         JOIN producto p ON c.id_producto = p.id_producto
         WHERE c.person_id = :pid
         ORDER BY c.fecha_compra DESC
         LIMIT 50'
    );
    $stmt->execute(['pid' => $personId]);
    $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse($compras);
} catch (Exception $e) {
    jsonResponse(['error' => 'Error de servidor'], 500);
}
