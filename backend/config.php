<?php
define('BANK_API_URL', 'http://host.docker.internal:8083/api/account');

function getDB(): PDO
{
    $host = "postgres";
    $port = "5432";
    $dbname = "bankdb";
    $user = "postgres";
    $password = "postgres";

    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return $conn;
}

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function getBankData(string $personId): ?array
{
    $url = BANK_API_URL . '/' . urlencode($personId);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;
    return json_decode($response, true);
}

function deductFromBank(string $personId, float $amount, string $reason): ?array
{
    $url = BANK_API_URL . '/' . urlencode($personId) . '/deduct';
    $body = json_encode(['amount' => $amount, 'reason' => $reason]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;
    return json_decode($response, true);
}
