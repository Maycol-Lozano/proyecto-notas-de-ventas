<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID de nota no válido.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notas_venta WHERE id = :id");
$stmt->execute(['id' => $id]);
$nota = $stmt->fetch();

if (!$nota) {
    echo "Nota de venta no encontrada.";
    exit;
}

// RUC del Emisor / Empresa
$ruc_emisor = "20601234567"; // Puedes cambiar este RUC por el tuyo

// Texto para generar el código QR con RUC, datos de la nota y verificación
$qr_data = "RUC: " . $ruc_emisor . " | NOTA: " . $nota['numero_nota'] . " | FECHA: " . $nota['fecha_emision'] . " | CLIENTE: " . $nota['cliente_nombre'] . " | DOC: " . ($nota['cliente_documento'] ?? '-') . " | TOTAL: S/ " . number_format($nota['monto_total'], 2);
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qr_data);

// Generar mensaje para enviar por WhatsApp
$mensaje_ws = "Hola *" . $nota['cliente_nombre'] . "*, adjuntamos el resumen de tu Nota de Venta N° *" . $nota['numero_nota'] . "*:\n\n"
            . "📋 *N° Nota:* " . $nota['numero_nota'] . "\n"
            . "👤 *Cliente:* " . $nota['cliente_nombre'] . "\n"
            . "🆔 *DNI/RUC:* " . ($nota['cliente_documento'] ?? '-') . "\n"
            . "💳 *Forma de Pago:* " . $nota['forma_pago'] . "\n"
            . "💰 *Total a Pagado:* S/ " . number_format($nota['monto_total'], 2) . "\n\n"
            . "¡Gracias por tu compra en *BARBODEGA ROZAGA*!";

$ws_link = "https://api.whatsapp.com/send?text=" . urlencode($mensaje_ws);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket N° <?= htmlspecialchars($nota['numero_nota']) ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .linea { border-bottom: 1px dashed #000; margin: 8px 0; }
        .bold { font-weight: bold; }
        .total-box { font-size: 14px; font-weight: bold; margin-top: 5px; }
        
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-print { background: #2563eb; color: white; }
        .btn-ws { background: #25D366; color: white; }
        .btn-close { background: #64748b; color: white; }

        @media print {
            .no-print { display: none; }
            body { width: 100%; margin: 0; }
        }
    </style>
</head>
<body onload="window.print();">

    <!-- BOTONES DE ACCIÓN (NO SE IMPRIMEN) -->
    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print();" class="btn btn-print">🖨️ Imprimir</button>
        <a href="<?= $ws_link ?>" target="_blank" class="btn btn-ws">📱 Enviar WhatsApp</a>
        <button onclick="window.close();" class="btn btn-close">✖ Cerrar</button>
    </div>

    <!-- CABECERA DEL COMPROBANTE -->
    <div class="text-center">
        <div style="font-size: 15px;" class="bold">BARBODEGA ROZAGA</div>
        <div>RUC: <?= $ruc_emisor ?></div>
        <div>Abarrotes • Licores • Golosinas</div>
        <div>Belén / Iquitos - Perú</div>
    </div>

    <div class="linea"></div>

    <!-- DATOS DE LA VENTA Y CLIENTE -->
    <div>
        <strong>NOTA DE VENTA:</strong> <?= htmlspecialchars($nota['numero_nota']) ?><br>
        <strong>FECHA:</strong> <?= date('d/m/Y H:i', strtotime($nota['created_at'] ?? $nota['fecha_emision'])) ?><br>
        <strong>CLIENTE:</strong> <?= htmlspecialchars($nota['cliente_nombre']) ?><br>
        <strong>RUC / DNI:</strong> <?= htmlspecialchars($nota['cliente_documento'] ?? 'SIN DOC') ?><br>
        <strong>FORMA PAGO:</strong> <?= htmlspecialchars($nota['forma_pago']) ?>
    </div>

    <div class="linea"></div>

    <!-- DETALLE DE COMPRA -->
    <table style="width: 100%; text-align: left; font-size: 11px;">
        <thead>
            <tr>
                <th>DESCRIPCIÓN</th>
                <th class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consumo general / Venta de productos</td>
                <td class="text-right">S/ <?= number_format($nota['monto_total'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="linea"></div>

    <div class="text-right total-box">
        TOTAL A PAGAR: S/ <?= number_format($nota['monto_total'], 2) ?>
    </div>

    <div class="linea"></div>

    <!-- CÓDIGO QR DE VERIFICACIÓN CON RUC Y DATOS -->
    <div class="text-center" style="margin: 10px 0;">
        <img src="<?= $qr_url ?>" alt="Código QR de Verificación" style="width: 110px; height: 110px; margin: 0 auto;">
        <div style="font-size: 9px; margin-top: 3px;">Escanea para verificar comprobante</div>
    </div>

    <div class="linea"></div>

    <!-- PIE Y CRÉDITOS DEL DESARROLLADOR -->
    <div class="text-center" style="font-size: 10px;">
        ¡Gracias por su preferencia!<br>
        *** Conservar para cualquier reclamo ***<br><br>
        -----------------------------------<br>
        <strong>Desarrollado por:</strong><br>
        Ing. Maycol Lozano Y.<br>
        <em>Sistema de Ventas YONINGUNO</em>
    </div>

</body>
</html>