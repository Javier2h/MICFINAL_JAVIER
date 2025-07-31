-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2025 a las 22:36:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `farmaciabd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriamedicamento`
--

CREATE TABLE `categoriamedicamento` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoriamedicamento`
--

INSERT INTO `categoriamedicamento` (`id_categoria`, `nombre_categoria`, `descripcion`) VALUES
(1, 'Antibióticos', 'Medicamentos para tratar infecciones bacterianas'),
(2, 'Analgésicos', 'Medicamentos para aliviar el dolor'),
(3, 'Antiinflamatorios', 'Reducen inflamación y dolor'),
(4, 'Antihistamínicos', 'Tratan alergias'),
(5, 'Antipiréticos', 'Reducen la fiebre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `cedula` varchar(10) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre_cliente`, `cedula`, `telefono`, `correo`, `fecha_registro`) VALUES
(1, 'Juan Pérez', '0911111111', '099111222', 'juan@example.com', '2024-01-15'),
(2, 'María López', '0922222222', '099222333', 'maria@example.com', '2024-02-20'),
(3, 'Carlos Ruiz', '0933333333', '099333444', 'carlos@example.com', '2024-03-10'),
(4, 'Ana Torres', '0944444444', '099444555', 'ana@example.com', '2024-04-05'),
(5, 'Luis Mendoza', '0955555555', '099555666', 'luis@example.com', '2024-05-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallereceta`
--

CREATE TABLE `detallereceta` (
  `id_detalle` int(11) NOT NULL,
  `id_receta` int(11) DEFAULT NULL,
  `id_medicamento` int(11) DEFAULT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `frecuencia` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallereceta`
--

INSERT INTO `detallereceta` (`id_detalle`, `id_receta`, `id_medicamento`, `dosis`, `frecuencia`) VALUES
(1, 1, 1, '500mg', 'Cada 8 horas'),
(2, 2, 5, '75mg', 'Cada 12 horas'),
(3, 3, 2, '500mg', 'Cada 6 horas'),
(4, 4, 2, '500mg', 'Cada 4 horas'),
(5, 5, 4, '10ml', 'Cada noche');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleventa`
--

CREATE TABLE `detalleventa` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_medicamento` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `subtotal` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleventa`
--

INSERT INTO `detalleventa` (`id_detalle`, `id_venta`, `id_medicamento`, `cantidad`, `subtotal`) VALUES
(1, 1, 1, 2, 5.00),
(2, 2, 5, 2, 3.00),
(3, 3, 2, 2, 1.00),
(4, 4, 4, 2, 6.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `nombre_empleado` varchar(100) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `fecha_contrato` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `nombre_empleado`, `cargo`, `turno`, `fecha_contrato`) VALUES
(1, 'Laura Sánchez', 'Farmacéutico', 'Mañana', '2023-01-10'),
(2, 'David Martínez', 'Cajero', 'Tarde', '2023-03-15'),
(3, 'Lucía Ramírez', 'Farmacéutico', 'Noche', '2023-06-20'),
(4, 'Pedro Castro', 'Administrador', 'Mañana', '2022-11-01'),
(5, 'Andrea Navas', 'Auxiliar', 'Tarde', '2023-09-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamento`
--

CREATE TABLE `medicamento` (
  `id_medicamento` int(11) NOT NULL,
  `nombre_medicamento` varchar(100) DEFAULT NULL,
  `principio_activo` varchar(100) DEFAULT NULL,
  `presentacion` varchar(50) DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `precio_unitario` decimal(5,2) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamento`
--

INSERT INTO `medicamento` (`id_medicamento`, `nombre_medicamento`, `principio_activo`, `presentacion`, `fecha_caducidad`, `stock`, `precio_unitario`, `id_proveedor`, `id_categoria`) VALUES
(1, 'Amoxicilina', 'Amoxicilina', 'Cápsula', '2026-06-01', 100, 2.50, 1, 1),
(2, 'Paracetamol', 'Paracetamol', 'Tableta', '2025-12-31', 200, 0.50, 2, 5),
(3, 'Ibuprofeno', 'Ibuprofeno', 'Tableta', '2025-08-15', 150, 0.80, 3, 3),
(4, 'Loratadina', 'Loratadina', 'Jarabe', '2026-01-01', 50, 3.00, 4, 4),
(5, 'Diclofenaco', 'Diclofenaco sódico', 'Inyectable', '2026-10-20', 75, 1.50, 5, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) DEFAULT NULL,
  `ruc` varchar(13) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_proveedor`, `ruc`, `direccion`, `telefono`, `correo_electronico`) VALUES
(1, 'PharmaCorp', '0999999999001', 'Av. Salud y Vida 123', '099111222', 'contacto@pharma.com'),
(2, 'MediExpress', '0988888888002', 'Calle 10 #45', '098123456', 'ventas@mediexpress.com'),
(3, 'Farmain', '0977777777003', 'Ruta 8 km 10', '097987654', 'info@farmain.com'),
(4, 'Distribuidora Vital', '0966666666004', 'Zona Industrial', '096456789', 'ventas@vital.com'),
(5, 'BioSalud', '0955555555005', 'Av. Principal #50', '095321654', 'bio@salud.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta`
--

CREATE TABLE `receta` (
  `id_receta` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `nombre_medico` varchar(100) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receta`
--

INSERT INTO `receta` (`id_receta`, `id_cliente`, `nombre_medico`, `diagnostico`, `fecha_emision`) VALUES
(1, 1, 'Dr. Julio Narváez', 'Infección respiratoria', '2024-06-01'),
(2, 2, 'Dra. Carmen León', 'Dolor muscular', '2024-06-02'),
(3, 3, 'Dr. Andrés Vera', 'Gripe común', '2024-06-03'),
(4, 4, 'Dra. Paola Núñez', 'Fiebre alta', '2024-06-04'),
(5, 5, 'Dr. Mario Silva', 'Alergia estacional', '2024-06-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) DEFAULT NULL,
  `contrasena_encriptada` varchar(100) DEFAULT NULL,
  `rol` varchar(20) DEFAULT NULL,
  `estado` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_usuario`, `contrasena_encriptada`, `rol`, `estado`) VALUES
(1, 'admin', 'admin_123', 'Administrador', 'activo'),
(2, 'dev', 'gestor_456', 'Desarrollador', 'activo'),
(3, 'super', 'consulta_789', 'Supervisor', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `total` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id_venta`, `id_cliente`, `id_empleado`, `fecha_venta`, `total`) VALUES
(1, 1, 1, '2024-07-01 09:00:00', 5.00),
(2, 2, 2, '2024-07-01 10:30:00', 3.00),
(3, 3, 3, '2024-07-02 15:00:00', 1.60),
(4, 4, 1, '2024-07-03 16:45:00', 6.00),
(5, 4, 2, '2025-07-22 01:43:00', 0.01);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoriamedicamento`
--
ALTER TABLE `categoriamedicamento`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `detallereceta`
--
ALTER TABLE `detallereceta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `detallereceta_ibfk_1` (`id_receta`),
  ADD KEY `detallereceta_ibfk_2` (`id_medicamento`);

--
-- Indices de la tabla `detalleventa`
--
ALTER TABLE `detalleventa`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `detalleventa_ibfk_1` (`id_venta`),
  ADD KEY `detalleventa_ibfk_2` (`id_medicamento`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`);

--
-- Indices de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  ADD PRIMARY KEY (`id_medicamento`),
  ADD KEY `medicamento_ibfk_1` (`id_proveedor`),
  ADD KEY `medicamento_ibfk_2` (`id_categoria`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `receta`
--
ALTER TABLE `receta`
  ADD PRIMARY KEY (`id_receta`),
  ADD KEY `receta_ibfk_1` (`id_cliente`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `venta_ibfk_1` (`id_cliente`),
  ADD KEY `venta_ibfk_2` (`id_empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoriamedicamento`
--
ALTER TABLE `categoriamedicamento`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detallereceta`
--
ALTER TABLE `detallereceta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalleventa`
--
ALTER TABLE `detalleventa`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `medicamento`
--
ALTER TABLE `medicamento`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `receta`
--
ALTER TABLE `receta`
  MODIFY `id_receta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallereceta`
--
ALTER TABLE `detallereceta`
  ADD CONSTRAINT `detallereceta_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `receta` (`id_receta`) ON DELETE CASCADE,
  ADD CONSTRAINT `detallereceta_ibfk_2` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento` (`id_medicamento`);

--
-- Filtros para la tabla `detalleventa`
--
ALTER TABLE `detalleventa`
  ADD CONSTRAINT `detalleventa_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalleventa_ibfk_2` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento` (`id_medicamento`);

--
-- Filtros para la tabla `medicamento`
--
ALTER TABLE `medicamento`
  ADD CONSTRAINT `medicamento_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  ADD CONSTRAINT `medicamento_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoriamedicamento` (`id_categoria`);

--
-- Filtros para la tabla `receta`
--
ALTER TABLE `receta`
  ADD CONSTRAINT `receta_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
