<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM notas_venta WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header('Location: index.php');
exit;