-- Creación de base de datos

CREATE DATABASE parque_diversiones;
USE parque_diversiones;


-- Creación de tablas 

CREATE TABLE visitantes (
    id_visitante INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    edad INT NOT NULL CHECK (edad >= 0),
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL
);


CREATE TABLE atracciones (
    id_atracciones INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado ENUM('Activo', 'En Mantenimiento', 'Inactivo') DEFAULT 'Activo'
);


CREATE TABLE mantenimientos (
    id_mantenimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_atracciones INT NOT NULL,
    fecha DATE NOT NULL,
    descripcion_mant TEXT,
    FOREIGN KEY (id_atracciones) REFERENCES atracciones(id_atracciones)
);



CREATE TABLE entradas (
    id_entrada INT AUTO_INCREMENT PRIMARY KEY,
    id_visitante INT NOT NULL,
    fecha DATE NOT NULL,
    precio DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_visitante) REFERENCES visitantes(id_visitante)
);



CREATE TABLE eventos (
    id_eventos INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL
);



CREATE TABLE entradas_eventos (
    id_entr_evento INT AUTO_INCREMENT PRIMARY KEY,
    id_entrada INT NOT NULL,
    id_eventos INT NOT NULL,
    FOREIGN KEY (id_entrada) REFERENCES entradas(id_entrada),
    FOREIGN KEY (id_eventos) REFERENCES eventos(id_eventos)
);




CREATE TABLE visitas_atracciones (
    id_visita INT AUTO_INCREMENT PRIMARY KEY,
    id_visitante INT NOT NULL,
    id_atracciones INT NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_visitante) REFERENCES visitantes(id_visitante),
    FOREIGN KEY (id_atracciones) REFERENCES atracciones(id_atracciones)
);



CREATE TABLE empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(150),
    correo VARCHAR(100) UNIQUE,
    telefono VARCHAR(20)
);



CREATE TABLE asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    actividad VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
);


CREATE TABLE empleados_eventos (
    id_emp_evento INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    id_eventos INT NOT NULL,
    rol_en_evento VARCHAR(100),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
    FOREIGN KEY (id_eventos) REFERENCES eventos(id_eventos)
);


CREATE TABLE usuario (
  id_usuario INT(11) NOT NULL,
  nombre_usuario VARCHAR(50) DEFAULT NULL,
  contrasena VARCHAR(100) DEFAULT NULL,
  rol VARCHAR(20) DEFAULT NULL,
  estado VARCHAR(10) DEFAULT NULL
);


-- INSERTAR VALORES A LAS TABLAS.


INSERT INTO visitantes (cedula, nombre, edad, genero) 
VALUES ('0102030405', 'Juan Pérez', 25, 'Masculino');

INSERT INTO visitantes (cedula, nombre, edad, genero) 
VALUES ('0607080910', 'María García', 30, 'Femenino');

INSERT INTO atracciones (nombre, descripcion, estado) 
VALUES ('Montaña Rusa', 'Atracción extrema para adultos', 'Activo');

INSERT INTO atracciones (nombre, descripcion, estado) 
VALUES ('Carrusel', 'Atracción infantil giratoria', 'Activo');

INSERT INTO mantenimientos (id_atracciones, fecha, descripcion_mant) 
VALUES (1, '2025-07-01', 'Cambio de frenos de seguridad');

INSERT INTO mantenimientos (id_atracciones, fecha, descripcion_mant) 
VALUES (2, '2025-07-10', 'Lubricación del eje central');

INSERT INTO entradas (id_visitante, fecha, precio) 
VALUES (1, '2025-07-15', 10.00);

INSERT INTO entradas (id_visitante, fecha, precio) 
VALUES (2, '2025-07-15', 10.00);

INSERT INTO eventos (nombre, descripcion, fecha) 
VALUES ('Noche de Magia', 'Espectáculo de magia para toda la familia', '2025-07-20');

INSERT INTO eventos (nombre, descripcion, fecha) 
VALUES ('Concierto Juvenil', 'Presentación de bandas locales', '2025-07-25');

INSERT INTO entradas_eventos (id_entrada, id_eventos) 
VALUES (1, 1);

INSERT INTO entradas_eventos (id_entrada, id_eventos) 
VALUES (2, 2);

INSERT INTO visitas_atracciones (id_visitante, id_atracciones, fecha) 
VALUES (1, 1, '2025-07-15');

INSERT INTO visitas_atracciones (id_visitante, id_atracciones, fecha) 
VALUES (2, 2, '2025-07-15');


INSERT INTO visitas_atracciones (id_visitante, id_atracciones, fecha) 
VALUES (1, 1, '2025-07-15');

INSERT INTO visitas_atracciones (id_visitante, id_atracciones, fecha) 
VALUES (2, 2, '2025-07-15');

INSERT INTO empleados (nombre, direccion, correo, telefono)
VALUES ('Carlos Herrera', 'Av. Amazonas 123', 'carlos@parque.com', '0991234567');

INSERT INTO empleados (nombre, direccion, correo, telefono)
VALUES ('Lucía Mena', 'Calle Bolívar 456', 'lucia@parque.com', '0987654321');


INSERT INTO asignaciones (id_empleado, actividad, fecha) 
VALUES (1, 'Supervisión de seguridad', '2025-07-15');

INSERT INTO asignaciones (id_empleado, actividad, fecha) 
VALUES (2, 'Guía en el Carrusel', '2025-07-15');


INSERT INTO empleados_eventos (id_empleado, id_eventos, rol_en_evento) 
VALUES (1, 1, 'Coordinador de logística');

INSERT INTO empleados_eventos (id_empleado, id_eventos, rol_en_evento) 
VALUES (2, 2, 'Animadora principal');



INSERT INTO usuario (nombre_usuario, contrasena, rol, estado)
VALUES ('admin', '$2y$10$ACw6OuYZ6lF4EVeBxCP3qezyzlwOfYssjmUUVVMm3uP/TpQAgTMnG', 'Administrador', 'activo');


-- Creación de usuarios y asignación de permisos 

-- Crear usuario administrador se coloca dependiendo el casos de los usuarios
CREATE USER 'administrador'@'localhost' IDENTIFIED BY 'admin123';
-- Crear usuario desarrollador
CREATE USER 'develop'@'localhost' IDENTIFIED BY 'develop123';
-- Crear usuario supervisor
CREATE USER 'supervisor'@'localhost' IDENTIFIED BY 'supervisor123';



-- Permisos para el Administrador
GRANT ALL PRIVILEGES ON parque_diversiones.* TO 'administrador'@'localhost';

-- Permisos para el Desarrollador
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.visitantes TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.atracciones TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.mantenimientos TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.entradas TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.eventos TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.entradas_eventos TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.visitas_atracciones TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.empleados TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON parque_diversiones.asignaciones TO 'develop'@'localhost';


-- Permisos para el Usuario Supervisor Inventario, detalle orden 
GRANT SELECT ON parque_diversiones.visitantes TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.atracciones TO 'supervisor'@'localhost';
GRANT SELECT  ON parque_diversiones.mantenimientos TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.entradas TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.eventos TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.entradas_eventos TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.visitas_atracciones TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.empleados TO 'supervisor'@'localhost';
GRANT SELECT ON parque_diversiones.asignaciones TO 'supervisor'@'localhost';



FLUSH PRIVILEGES;

