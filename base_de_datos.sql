USE sistema_notas;

-- 1. Eliminar el usuario anterior
DELETE FROM usuarios WHERE email = 'maycol@gmail.com';

-- 2. Insertar usuario con la contraseña '123456' encriptada
INSERT INTO usuarios (nombre, email, password) VALUES 
('MAYCOL LOZANO', 'maycol@gmail.com', '$2y$10$e.PqNpxT.LpC4O6.wz6vve5G5qR3X8A4S4XfK4L4oN4jO3v0xK.7y');