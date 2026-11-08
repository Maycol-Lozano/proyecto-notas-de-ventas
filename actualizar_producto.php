<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE productos SET 
            codigo_interno = :cod_int, 
            codigo_barras  = :cod_bar, 
            nombre         = :nom, 
            unidad_medida  = :uni, 
            marca          = :mar, 
            categoria      = :cat, 
            stock_actual   = :stk_act, 
            stock_minimo   = :stk_min, 
            stock_maximo   = :stk_max, 
            precio_compra  = :p_comp, 
            precio_unidad  = :p_uni, 
            precio_mayor   = :p_may, 
            precio_docena  = :p_doc, 
            precio_ciento  = :p_cien
            WHERE id = :id");

        $stmt->execute([
            'id'      => $id,
            'cod_int' => trim($_POST['codigo_interno']),
            'cod_bar' => trim($_POST['codigo_barras']),
            'nom'     => trim($_POST['nombre']),
            'uni'     => $_POST['unidad_medida'],
            'mar'     => trim($_POST['marca']),
            'cat'     => trim($_POST['categoria']),
            'stk_act' => (int)$_POST['stock_actual'],
            'stk_min' => (int)$_POST['stock_minimo'],
            'stk_max' => (int)$_POST['stock_maximo'],
            'p_comp'  => (float)$_POST['precio_compra'],
            'p_uni'   => (float)$_POST['precio_unidad'],
            'p_may'   => (float)$_POST['precio_mayor'],
            'p_doc'   => (float)$_POST['precio_docena'],
            'p_cien'  => (float)$_POST['precio_ciento']
        ]);
    }
}

header('Location: productos.php');
exit;