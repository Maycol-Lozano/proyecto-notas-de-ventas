<?php
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE notas_venta 
                           SET numero_nota = :num, cliente_nombre = :cli, cliente_documento = :doc, 
                               fecha_emision = :fec, forma_pago = :fpago, monto_total = :monto, 
                               observacion = :obs, estado = :est 
                           WHERE id = :id");
    $stmt->execute([
        'num'   => trim($_POST['numero_nota']),
        'cli'   => trim($_POST['cliente_nombre']),
        'doc'   => trim($_POST['cliente_documento']),
        'fec'   => $_POST['fecha_emision'],
        'fpago' => $_POST['forma_pago'],
        'monto' => (float)$_POST['monto_total'],
        'obs'   => trim($_POST['observacion']),
        'est'   => $_POST['estado'],
        'id'    => (int)$_POST['id']
    ]);
}

header('Location: index.php');
exit;