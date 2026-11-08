<?php
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO notas_venta (numero_nota, cliente_nombre, cliente_documento, fecha_emision, forma_pago, monto_total, observacion, estado) 
                           VALUES (:num, :cli, :doc, :fec, :fpago, :monto, :obs, :est)");
    $stmt->execute([
        'num'   => trim($_POST['numero_nota']),
        'cli'   => trim($_POST['cliente_nombre']),
        'doc'   => trim($_POST['cliente_documento']),
        'fec'   => $_POST['fecha_emision'],
        'fpago' => $_POST['forma_pago'],
        'monto' => (float)$_POST['monto_total'],
        'obs'   => trim($_POST['observacion']),
        'est'   => $_POST['estado']
    ]);
}

header('Location: index.php');
exit;