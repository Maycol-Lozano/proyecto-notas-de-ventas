<?php
// config/conexion.php
$host = 'sql302.infinityfree.com';
$db   = 'if0_43119203_sistema';
$user = 'if0_43119203';
$pass = 'maycolito02';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}