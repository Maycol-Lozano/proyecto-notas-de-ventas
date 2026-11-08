<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - BARBODEGA ROZAGA ABARROTES • LICORES • GOLOSINAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-800">

        <!-- COLUMNA IZQUIERDA -->
        <div class="relative bg-slate-900 text-white p-8 flex flex-col justify-between overflow-hidden min-h-[420px] md:min-h-[500px]">
            <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=1000&auto=format&fit=crop"
                alt="Fondo Tienda"
                class="absolute inset-0 w-full h-full object-cover opacity-25">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

            <div class="relative z-10 space-y-4 my-auto">
                <h1 class="text-3xl md:text-4xl font-black leading-tight tracking-tight">
                    Sistema de Gestión Empresarial <br>
                    <span class="text-indigo-400">YONINGUNO</span>
                </h1>
                <p class="text-xs text-slate-300 font-medium leading-relaxed max-w-xs">
                    Controla ventas, inventario, finanzas, clientes, compras, utilidad neta y reportes personalizados desde un solo panel centralizado.
                </p>

                <div class="pt-4 grid grid-cols-3 gap-2 border-t border-slate-700/50 text-[11px] font-bold text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <span class="text-amber-400">⚡</span> Facturación
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-indigo-400">🖥️</span> Local / Web
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-emerald-400">🌐</span> Instalación Web
                    </div>
                </div>

                <div class="pt-2">
                    <span class="text-xl font-black text-white">100%</span>
                    <span class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest block">SUNAT</span>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="p-8 md:p-10 flex flex-col justify-between bg-gradient-to-b from-indigo-50/40 to-white">

            <div class="text-center space-y-2">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md">🍻</div>
                    <span class="font-black text-2xl text-slate-900 tracking-wider">BARBODEGA ROZAGA</span>
                </div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">BARBODEGA ROZAGA ABARROTES • LICORES • GOLOSINAS</h2>
                <p class="text-[11px] text-slate-400 font-semibold">Sistema de Gestión Empresarial @YONINGUNO</p>
            </div>

            <form action="procesar_login.php" method="POST" class="space-y-4 my-6" x-data="{ showPass: false }">

                <?php if ($error): ?>
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl text-xs font-bold text-center">
                        ⚠️ Credenciales incorrectas. Intenta de nuevo.
                    </div>
                <?php endif; ?>

                <div class="space-y-1">
                    <h3 class="text-lg font-extrabold text-slate-900">Bienvenido 👋</h3>
                    <p class="text-xs text-slate-400 font-medium">Ingresa tus credenciales para continuar</p>
                </div>

                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Correo Electrónico</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-indigo-400 text-xs"></i>
                            <input type="email" name="email" required value="maycol@gmail.com" class="w-full bg-indigo-50/50 border border-indigo-100 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contraseña</label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-3.5 top-3 text-amber-500 text-xs"></i>
                            <input :type="showPass ? 'text' : 'password'" name="password" required value="123456" class="w-full bg-indigo-50/50 border border-indigo-100 rounded-xl pl-9 pr-10 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 text-xs">
                                <i :class="showPass ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2 space-y-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                        🔒 <span>Iniciar Sesión</span>
                    </button>
                </div>
            </form>

            <div class="text-center">
                <span class="text-[10px] text-slate-400 font-medium">© <?= date('Y') ?> - YONINGUNO • v1.0.0</span>
            </div>

        </div>

    </div>

</body>

</html>