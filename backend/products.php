<?php
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    $stmt = $db->query(
        'SELECT p.*, c.nombre as categoria_nombre
         FROM producto p
         JOIN categoria c ON p.id_categoria = c.id_categoria
         ORDER BY p.id_producto'
    );
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse($products);
} catch (Exception $e) {
    jsonResponse(['error' => 'Error al cargar productos'], 500);
}
