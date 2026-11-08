<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO productos (
        codigo_interno, codigo_barras, nombre, unidad_medida, 
        marca, categoria, stock_actual, stock_minimo, stock_maximo, 
        precio_compra, precio_unidad, precio_mayor, precio_docena, precio_ciento
    ) VALUES (
        :cod_int, :cod_bar, :nom, :uni, 
        :mar, :cat, :stk_act, :stk_min, :stk_max, 
        :p_comp, :p_uni, :p_may, :p_doc, :p_cien
    )");

    $stmt->execute([
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

header('Location: productos.php');
exit;