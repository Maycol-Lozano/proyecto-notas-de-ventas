<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM notas_venta WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$nota = $stmt->fetch();

if (!$nota) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Nota de Venta - DINEX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-8 flex justify-center items-center min-h-screen">

    <div class="bg-white w-full max-w-2xl rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-indigo-400"></i> Editar Nota: <?= htmlspecialchars($nota['numero_nota']) ?></h2>
            <a href="index.php" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 py-1.5 px-3 rounded-lg border border-slate-700">← Volver</a>
        </div>

        <form action="actualizar_nota.php" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" value="<?= $nota['id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">N° Nota *</label>
                    <input type="text" name="numero_nota" value="<?= htmlspecialchars($nota['numero_nota']) ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-mono font-bold text-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Fecha de Emisión *</label>
                    <input type="date" name="fecha_emision" value="<?= $nota['fecha_emision'] ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nombre del Cliente *</label>
                    <input type="text" name="cliente_nombre" value="<?= htmlspecialchars($nota['cliente_nombre']) ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">DNI / RUC</label>
                    <input type="text" name="cliente_documento" value="<?= htmlspecialchars($nota['cliente_documento'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Forma de Pago</label>
                    <select name="forma_pago" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs">
                        <option value="Efectivo" <?= $nota['forma_pago'] === 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="Yape / Plin" <?= $nota['forma_pago'] === 'Yape / Plin' ? 'selected' : '' ?>>Yape / Plin</option>
                        <option value="Tarjeta / Pos" <?= $nota['forma_pago'] === 'Tarjeta / Pos' ? 'selected' : '' ?>>Tarjeta / Pos</option>
                        <option value="Transferencia" <?= $nota['forma_pago'] === 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Monto Total (S/) *</label>
                    <input type="number" step="0.01" name="monto_total" value="<?= $nota['monto_total'] ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Estado</label>
                    <select name="estado" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold">
                        <option value="Emitida" <?= $nota['estado'] === 'Emitida' ? 'selected' : '' ?>>Emitida</option>
                        <option value="Pagada" <?= $nota['estado'] === 'Pagada' ? 'selected' : '' ?>>Pagada</option>
                        <option value="Anulada" <?= $nota['estado'] === 'Anulada' ? 'selected' : '' ?>>Anulada</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Observación</label>
                <textarea name="observacion" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs"><?= htmlspecialchars($nota['observacion'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <a href="index.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg">Cancelar</a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow">Actualizar Cambios</button>
            </div>
        </form>
    </div>

</body>
</html>