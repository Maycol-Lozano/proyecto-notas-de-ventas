<?php
date_default_timezone_set('America/Lima');
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

$buscar = trim($_GET['buscar'] ?? '');

$sql = "SELECT * FROM productos WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $sql .= " AND (nombre LIKE :b1 OR codigo_interno LIKE :b2 OR codigo_barras LIKE :b3)";
    $params['b1'] = "%$buscar%";
    $params['b2'] = "%$buscar%";
    $params['b3'] = "%$buscar%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

$total_productos = count($productos);
$total_notas = $pdo->query("SELECT COUNT(*) FROM notas_venta")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos - BARBODEGA ROZAGA </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-bg {
            background-color: #0f172a;
        }
    </style>
</head>

<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-700" x-data="{ invOpen: true }">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar-bg text-slate-300 flex flex-col justify-between flex-shrink-0 z-30 shadow-2xl">
        <div>
            <div class="p-5 border-b border-slate-800 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">🍻</div>
                    <span class="font-black text-xl text-white tracking-wider">BARBODEGA ROZAGA</span>
                </div>
                <div class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">• ALMACÉN •</div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 transition">
                    <i class="fa-solid fa-house text-slate-400 w-5"></i>
                    <span>Dashboard</span>
                </a>

                <a href="index.php" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-invoice-dollar text-emerald-400 w-5"></i>
                        <span>Notas de Venta</span>
                    </div>
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold px-2 py-0.5 rounded-full"><?= $total_notas ?></span>
                </a>

                <div>
                    <button @click="invOpen = !invOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-800 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-industry text-amber-400 w-5"></i>
                            <span>Inventario</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-amber-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full"><?= $total_productos ?></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="invOpen ? 'rotate-180' : ''"></i>
                        </div>
                    </button>

                    <div x-show="invOpen" x-transition class="mt-1 pl-11 pr-2 space-y-1">
                        <a href="productos.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 transition">
                            📦 <span>Productos</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800">
            <a href="logout.php" onclick="return confirm('¿Seguro que deseas cerrar sesión?');" class="w-full bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- CONTENIDO -->
    <div class="flex-1 flex flex-col overflow-y-auto">

        <header class="bg-white border-b border-slate-200 px-8 py-3 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-lg font-extrabold text-slate-900">Productos</h1>
                <p class="text-xs text-slate-400 font-medium"><?= date('l, d F Y') ?></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-emerald-600 text-white text-[11px] font-black px-2.5 py-1 rounded">PRODUCCIÓN</span>
                <span class="bg-emerald-600 text-white text-[11px] font-black px-2.5 py-1 rounded">✓ SUNAT</span>
            </div>
        </header>

        <main class="p-8">

            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">📦 Productos</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5"><?= $total_productos ?> productos registrados</p>
                </div>
                <a href="nuevo_producto.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black py-2.5 px-4 rounded-xl flex items-center gap-2 shadow transition">
                    <i class="fa-solid fa-plus"></i> Nuevo Producto
                </a>
            </div>

            <!-- Filtros -->
            <form method="GET" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3 mb-6">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Buscar por nombre, código..." class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow transition">Filtrar</button>
                <a href="productos.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs py-2 px-4 rounded-lg">Limpiar</a>
            </form>

            <!-- Tabla -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase">
                            <th class="py-3 px-4">CÓDIGO</th>
                            <th class="py-3 px-4">PRODUCTO</th>
                            <th class="py-3 px-4">CATEGORÍA</th>
                            <th class="py-3 px-4">P. COMPRA</th>
                            <th class="py-3 px-4">P. VENTA</th>
                            <th class="py-3 px-4">STOCK</th>
                            <th class="py-3 px-4 text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                        <?php if (count($productos) > 0): ?>
                            <?php foreach ($productos as $p): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-slate-500"><?= htmlspecialchars($p['codigo_interno']) ?></td>
                                    <td class="py-3 px-4 font-black text-slate-900 uppercase"><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td class="py-3 px-4 text-slate-400"><?= htmlspecialchars($p['categoria']) ?></td>
                                    <td class="py-3 px-4 font-mono text-slate-500">S/ <?= number_format($p['precio_compra'], 2) ?></td>
                                    <td class="py-3 px-4 font-mono font-black text-emerald-600">S/ <?= number_format($p['precio_unidad'], 2) ?></td>
                                    <td class="py-3 px-4">
                                        <span class="bg-emerald-100 text-emerald-700 font-extrabold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <?= $p['stock_actual'] ?> NIU
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 mx-auto shadow transition">
                                            Acciones <i class="fa-solid fa-caret-down"></i>
                                        </button>

                                        <!-- DROPDOWN DE OPCIONES ESTILO DINEX -->
                                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-4 mt-1 w-44 bg-white rounded-xl shadow-2xl border border-slate-100 py-2 z-50 text-left font-medium text-slate-700 divide-y divide-slate-100">
                                            <div class="py-1">
                                                <a href="editar_producto.php?id=<?= $p['id'] ?>" class="flex items-center gap-2 px-3 py-1.5 hover:bg-indigo-50 text-indigo-600 font-bold text-xs">
                                                    ✏️ <span>Editar</span>
                                                </a>
                                                <a href="nuevo_producto.php?duplicar=<?= $p['id'] ?>" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 text-slate-700 text-xs">
                                                    📋 <span>Duplicar</span>
                                                </a>
                                            </div>
                                            <div class="py-1">
                                                <a href="#" onclick="alert('Código de Barras: <?= $p['codigo_barras'] ?>')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 text-slate-700 text-xs">
                                                    📊 <span>Cód. Barras</span>
                                                </a>
                                                <a href="#" onclick="alert('Etiqueta impresa')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 text-slate-700 text-xs">
                                                    🏷️ <span>Etiquetas</span>
                                                </a>
                                            </div>
                                            <div class="py-1">
                                                <a href="#" onclick="alert('Producto inhabilitado')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-amber-50 text-amber-600 font-bold text-xs">
                                                    🚫 <span>Inhabilitar</span>
                                                </a>
                                                <a href="eliminar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar producto?')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-rose-50 text-rose-600 font-bold text-xs">
                                                    🗑️ <span>Eliminar</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400 font-medium">No hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>

</html>