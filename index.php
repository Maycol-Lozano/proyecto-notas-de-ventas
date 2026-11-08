<?php
date_default_timezone_set('America/Lima');
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

$buscar = trim($_GET['buscar'] ?? '');

$sql = "SELECT * FROM notas_venta WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $sql .= " AND (cliente_nombre LIKE :b1 OR cliente_documento LIKE :b2 OR numero_nota LIKE :b3)";
    $params['b1'] = "%$buscar%";
    $params['b2'] = "%$buscar%";
    $params['b3'] = "%$buscar%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notas = $stmt->fetchAll();

$total_notas = count($notas);
$total_recaudado = $pdo->query("SELECT SUM(monto_total) FROM notas_venta WHERE estado != 'Anulada'")->fetchColumn() ?? 0;
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Notas de Venta - ROZAGA</title>
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

    <!-- SIDEBAR COMPLETO -->
    <aside class="w-64 sidebar-bg text-slate-300 flex flex-col justify-between flex-shrink-0 z-30 shadow-2xl">
        <div>
            <div class="p-5 border-b border-slate-800 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">🍻</div>
                    <span class="font-black text-xl text-white tracking-wider">
                        BARBODEGA ROZAGA
                    </span>
                </div>
                <div class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
                <div class="text-[10px] text-slate-500 font-semibold uppercase">Abarrotes • Licores • Golosinas</div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 transition">
                    <i class="fa-solid fa-house text-slate-400 w-5"></i>
                    <span>Dashboard</span>
                </a>

                <a href="index.php" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-800 transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-invoice-dollar text-emerald-400 w-5"></i>
                        <span>Notas de Venta</span>
                    </div>
                    <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full"><?= $total_notas ?></span>
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col overflow-y-auto">

        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Gestión de Notas de Venta</h1>
                <p class="text-xs text-slate-400 font-medium"><?= date('l, d F Y') ?></p>
            </div>
            <a href="nueva_nota.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black py-2.5 px-4 rounded-xl flex items-center gap-2 shadow transition">
                <i class="fa-solid fa-plus"></i> Nueva Nota
            </a>
        </header>

        <main class="p-8 space-y-6">

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Notas de Venta Registradas</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Monto Total Recaudado: <span class="text-emerald-600 font-bold">S/ <?= number_format($total_recaudado, 2) ?></span></p>
                </div>
            </div>

            <!-- Filtro de Búsqueda -->
            <form method="GET" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Buscar por cliente, RUC/DNI o N° de nota..." class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow transition">Filtrar</button>
                <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs py-2 px-4 rounded-lg">Limpiar</a>
            </form>

            <!-- Tabla de Notas de Venta -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase">
                            <th class="py-3 px-4">N° NOTA</th>
                            <th class="py-3 px-4">FECHA</th>
                            <th class="py-3 px-4">CLIENTE</th>
                            <th class="py-3 px-4">DNI / RUC</th>
                            <th class="py-3 px-4">FORMA PAGO</th>
                            <th class="py-3 px-4">TOTAL</th>
                            <th class="py-3 px-4">ESTADO</th>
                            <th class="py-3 px-4 text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                        <?php if (count($notas) > 0): ?>
                            <?php foreach ($notas as $n): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-indigo-600"><?= htmlspecialchars($n['numero_nota']) ?></td>
                                    <td class="py-3 px-4 text-slate-400"><?= date('d/m/Y', strtotime($n['fecha_emision'])) ?></td>
                                    <td class="py-3 px-4 font-black text-slate-900"><?= htmlspecialchars($n['cliente_nombre']) ?></td>
                                    <td class="py-3 px-4 font-mono text-slate-500"><?= htmlspecialchars($n['cliente_documento'] ?? '-') ?></td>
                                    <td class="py-3 px-4"><span class="bg-slate-100 px-2 py-0.5 rounded text-[11px]"><?= htmlspecialchars($n['forma_pago']) ?></span></td>
                                    <td class="py-3 px-4 font-mono font-black text-emerald-600">S/ <?= number_format($n['monto_total'], 2) ?></td>
                                    <td class="py-3 px-4">
                                        <?php if ($n['estado'] === 'Pagada'): ?>
                                            <span class="bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Pagada</span>
                                        <?php elseif ($n['estado'] === 'Emitida'): ?>
                                            <span class="bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Emitida</span>
                                        <?php else: ?>
                                            <span class="bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded-full text-[10px]">Anulada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-center relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 mx-auto transition">
                                            Opciones <i class="fa-solid fa-caret-down"></i>
                                        </button>

                                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-4 mt-1 w-36 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50 text-left font-medium text-slate-700">
                                            <a href="imprimir_nota.php?id=<?= $n['id'] ?>" target="_blank" class="flex items-center gap-2 px-3 py-1.5 hover:bg-blue-50 text-blue-600 font-bold text-xs">
                                                🖨️ <span>Imprimir</span>
                                            </a>
                                            <a href="editar_nota.php?id=<?= $n['id'] ?>" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-indigo-600 text-xs border-t border-slate-100">
                                                ✏️ <span>Editar</span>
                                            </a>
                                            <a href="eliminar_nota.php?id=<?= $n['id'] ?>" onclick="return confirm('¿Eliminar nota?')" class="flex items-center gap-2 px-3 py-1.5 hover:bg-rose-50 text-rose-600 text-xs border-t border-slate-100">
                                                🗑️ <span>Eliminar</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-400 font-medium">No se encontraron notas de venta.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>

</html>