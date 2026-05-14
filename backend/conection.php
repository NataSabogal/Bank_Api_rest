<?php

$host = "postgres";
$port = "5432";
$dbname = "bankdb";
$user = "postgres";
$password = "postgres";

try {

    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexion exitosa";

} catch(PDOException $e){

    echo "Error: " . $e->getMessage();
}