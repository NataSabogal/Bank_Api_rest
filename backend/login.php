<?php
require_once __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$name = trim($input['name'] ?? '');
$personId = trim($input['personId'] ?? '');

if (empty($name) || empty($personId)) {
    jsonResponse(['success' => false, 'error' => 'Todos los campos son obligatorios'], 400);
}

$account = getBankData($personId);
if (!$account) {
    jsonResponse(['success' => false, 'error' => 'PersonId no valido'], 401);
}

if (strcasecmp(trim($account['name']), $name) !== 0) {
    jsonResponse(['success' => false, 'error' => 'El nombre no coincide con el PersonId'], 401);
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'INSERT INTO usuario (person_id, nombre, ultimo_acceso)
         VALUES (:pid, :name, NOW())
         ON CONFLICT (person_id)
         DO UPDATE SET ultimo_acceso = NOW(), nombre = :name2'
    );
    $stmt->execute(['pid' => $personId, 'name' => $name, 'name2' => $name]);

    jsonResponse([
        'success' => true,
        'personId' => $personId,
        'name' => $account['name'],
        'balance' => $account['balance'],
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => 'Error de servidor'], 500);
}
