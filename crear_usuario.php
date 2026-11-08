<?php
require_once 'config/conexion.php';

// ✏️ CAMBIA AQUÍ LOS DATOS DEL USUARIO:
$nombre   = 'SINCHICUY';
$email    = 'cliente@gmail.com';
$password = '123456';

// Genera el hash de seguridad
$hash = password_hash($password, PASSWORD_DEFAULT);

// Elimina registros anteriores con ese mismo correo (si existía)
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE email = :email");
$stmt->execute(['email' => $email]);

// Inserta el nuevo usuario
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
$stmt->execute([
    'nombre'   => $nombre,
    'email'    => $email,
    'password' => $hash
]);

echo "<div style='font-family: sans-serif; padding: 25px; background: #dcfce7; color: #166534; border-radius: 12px; font-weight: bold;'>";
echo "<h2>✅ Usuario configurado exitosamente</h2>";
echo "<p>Nombre: <b>$nombre</b></p>";
echo "<p>Correo: <b>$email</b></p>";
echo "<p>Contraseña: <b>$password</b></p>";
echo "<a href='login.php' style='display:inline-block; margin-top:10px; padding:10px 20px; background:#16a34a; color:white; border-radius:8px; text-decoration:none;'>Ir a Iniciar Sesión →</a>";
echo "</div>";
?>