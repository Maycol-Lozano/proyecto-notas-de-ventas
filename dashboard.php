<?php
date_default_timezone_set('America/Lima');
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';
// ... resto del código ...

$total_notas = $pdo->query("SELECT COUNT(*) FROM notas_venta")->fetchColumn() ?? 0;
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?? 0;
$total_recaudado = $pdo->query("SELECT SUM(monto_total) FROM notas_venta WHERE estado != 'Anulada'")->fetchColumn() ?? 0;
$productos_bajo_stock = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock_actual <= stock_minimo")->fetchColumn() ?? 0;

$ventas_semana = $pdo->query("SELECT SUM(monto_total) FROM notas_venta WHERE estado != 'Anulada' AND YEARWEEK(fecha_emision, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn() ?? 0;
$notas_semana = $pdo->query("SELECT COUNT(*) FROM notas_venta WHERE YEARWEEK(fecha_emision, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn() ?? 0;

$ultimas_notas = $pdo->query("SELECT * FROM notas_venta ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - BARBODEGA ROZAGA</title>
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

<body class="bg-slate-50 flex h-screen overflow-hidden text-slate-700" x-data="{ invOpen: false }">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar-bg text-slate-300 flex flex-col justify-between flex-shrink-0 z-30 shadow-2xl">
        <div>
            <div class="p-5 border-b border-slate-800 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">🍻</div>
                    <span class="font-black text-xl text-white tracking-wider">BARBODEGA ROZAGA</span>
                </div>
                <div class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">• PANEL GENERAL •</div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-800 transition">
                    <i class="fa-solid fa-house text-blue-400 w-5"></i>
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
                    <button @click="invOpen = !invOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-industry text-amber-400 w-5"></i>
                            <span>Inventario</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-amber-500/20 text-amber-400 text-[10px] font-extrabold px-2 py-0.5 rounded-full"><?= $total_productos ?></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="invOpen ? 'rotate-180' : ''"></i>
                        </div>
                    </button>

                    <div x-show="invOpen" x-transition class="mt-1 pl-11 pr-2 space-y-1">
                        <a href="productos.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition">
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

        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Dashboard</h1>
                <p class="text-xs text-slate-400 font-medium"><?= date('l, d F Y') ?></p>
            </div>
            <span class="bg-blue-600 text-white text-xs font-black px-3 py-1 rounded-md tracking-wider">RESUMEN GENERAL</span>
        </header>

        <main class="p-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventas de la Semana</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">S/ <?= number_format($ventas_semana, 2) ?></h3>
                        <span class="text-[11px] text-slate-400 font-semibold"><?= $notas_semana ?> ventas registradas</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Recaudado</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">S/ <?= number_format($total_recaudado, 2) ?></h3>
                        <span class="text-[11px] text-slate-400 font-semibold"><?= $total_notas ?> notas emitidas</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Productos</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1"><?= $total_productos ?></h3>
                        <span class="text-[11px] text-slate-400 font-semibold">En catálogo</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bajo Stock</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1"><?= $productos_bajo_stock ?></h3>
                        <span class="text-[11px] text-rose-500 font-semibold">Stock bajo crítico</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>

            <!-- Tabla Últimas Notas -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i> Últimas Ventas Registradas
                    </h3>
                    <a href="index.php" class="text-xs text-blue-600 font-bold hover:underline">Ver todas →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3">N° NOTA</th>
                                <th class="py-2.5 px-3">CLIENTE</th>
                                <th class="py-2.5 px-3">MONTO</th>
                                <th class="py-2.5 px-3">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium">
                            <?php foreach ($ultimas_notas as $un): ?>
                                <tr>
                                    <td class="py-3 px-3 font-mono font-bold text-indigo-600"><?= htmlspecialchars($un['numero_nota']) ?></td>
                                    <td class="py-3 px-3 font-bold text-slate-900"><?= htmlspecialchars($un['cliente_nombre']) ?></td>
                                    <td class="py-3 px-3 font-mono font-bold text-emerald-600">S/ <?= number_format($un['monto_total'], 2) ?></td>
                                    <td class="py-3 px-3">
                                        <?php if ($un['estado'] === 'Pagada'): ?>
                                            <span class="bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Pagada</span>
                                        <?php elseif ($un['estado'] === 'Emitida'): ?>
                                            <span class="bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Emitida</span>
                                        <?php else: ?>
                                            <span class="bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Anulada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>

</html>