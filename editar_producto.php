<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: productos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - BARBODEGA ROZAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-8 flex justify-center items-center min-h-screen">

    <div class="bg-white w-full max-w-4xl rounded-2xl border border-slate-200 shadow-xl overflow-hidden" x-data="{ tab: 'datos', compra: <?= $producto['precio_compra'] ?>, unidad: <?= $producto['precio_unidad'] ?> }">
        
        <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="font-bold text-lg flex items-center gap-2">
                ✏️ Editar Producto: <span class="text-indigo-400"><?= htmlspecialchars($producto['nombre']) ?></span>
            </h2>
            <a href="productos.php" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 py-1.5 px-3 rounded-lg border border-slate-700">← Volver</a>
        </div>

        <form action="actualizar_producto.php" method="POST">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
            
            <div class="flex border-b border-slate-200 bg-slate-50 px-6 pt-3 gap-2">
                <button type="button" @click="tab = 'datos'" :class="tab === 'datos' ? 'bg-white border-x border-t border-slate-200 text-slate-900 font-bold border-b-2 border-b-indigo-600' : 'text-slate-500 font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg transition">
                    📋 Datos del producto
                </button>
                <button type="button" @click="tab = 'precios'" :class="tab === 'precios' ? 'bg-white border-x border-t border-slate-200 text-slate-900 font-bold border-b-2 border-b-indigo-600' : 'text-slate-500 font-medium'" class="px-4 py-2.5 text-xs rounded-t-lg transition">
                    💰 Precios y stock
                </button>
            </div>

            <div class="p-6">

                <!-- TAB 1: DATOS -->
                <div x-show="tab === 'datos'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Código Interno</label>
                            <input type="text" name="codigo_interno" value="<?= htmlspecialchars($producto['codigo_interno']) ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-mono font-bold text-indigo-600">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Código de Barras</label>
                            <input type="text" name="codigo_barras" value="<?= htmlspecialchars($producto['codigo_barras']) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-mono">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nombre del Producto *</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Unidad de Medida</label>
                            <select name="unidad_medida" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                                <option value="Unidad (NIU)" <?= $producto['unidad_medida'] === 'Unidad (NIU)' ? 'selected' : '' ?>>Unidad (NIU)</option>
                                <option value="Caja (BX)" <?= $producto['unidad_medida'] === 'Caja (BX)' ? 'selected' : '' ?>>Caja (BX)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Marca</label>
                            <input type="text" name="marca" value="<?= htmlspecialchars($producto['marca']) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Categoría</label>
                            <input type="text" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Stock Actual</label>
                            <input type="number" name="stock_actual" value="<?= $producto['stock_actual'] ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Stock Máximo</label>
                            <input type="number" name="stock_maximo" value="<?= $producto['stock_maximo'] ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold">
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PRECIOS -->
                <div x-show="tab === 'precios'" class="space-y-6">
                    <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 w-64">
                        <label class="block text-[10px] font-bold text-indigo-600 uppercase mb-1">Precio de Compra (S/)</label>
                        <input type="number" step="0.01" name="precio_compra" x-model.number="compra" class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm font-black text-indigo-700">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                            <label class="block text-[10px] font-bold text-emerald-700 uppercase mb-1">Precio Unidad (S/)</label>
                            <input type="number" step="0.01" name="precio_unidad" x-model="unidad" class="w-full bg-white border border-emerald-300 rounded-lg px-2 py-1.5 text-xs font-black text-emerald-600">
                        </div>

                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200">
                            <label class="block text-[10px] font-bold text-amber-700 uppercase mb-1">Precio x Mayor (S/)</label>
                            <input type="number" step="0.01" name="precio_mayor" value="<?= $producto['precio_mayor'] ?>" class="w-full bg-white border border-amber-300 rounded-lg px-2 py-1.5 text-xs font-black text-amber-600">
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Docena (S/)</label>
                            <input type="number" step="0.01" name="precio_docena" value="<?= $producto['precio_docena'] ?>" class="w-full bg-white border border-slate-300 rounded-lg px-2 py-1.5 text-xs font-black text-slate-800">
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Ciento (S/)</label>
                            <input type="number" step="0.01" name="precio_ciento" value="<?= $producto['precio_ciento'] ?>" class="w-full bg-white border border-slate-300 rounded-lg px-2 py-1.5 text-xs font-black text-slate-800">
                        </div>
                    </div>
                </div>

            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <a href="productos.php" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-lg shadow-lg">
                    💾 Guardar Cambios
                </button>
            </div>

        </form>
    </div>

</body>
</html>