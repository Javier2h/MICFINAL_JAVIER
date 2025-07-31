-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-08-2025 a las 00:03:44
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
-- Base de datos: `panaderia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriasproducto`
--

CREATE TABLE `categoriasproducto` (
  `categoria_id` int(11) NOT NULL,
  `nombre_categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoriasproducto`
--

INSERT INTO `categoriasproducto` (`categoria_id`, `nombre_categoria`) VALUES
(1, 'PANES'),
(2, 'PASTELES'),
(3, 'Bocaditos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`cliente_id`, `nombre`, `correo`, `telefono`, `direccion`) VALUES
(1, 'Denep Maliza', 'denepm29@gmail.com', '0986934918', 'Edén del Valle 2'),
(2, 'Akiren Mons', 'denepmn@gmail.com', '0986934918', 'Quito Pichincha'),
(3, 'Rexito', 'rexito@gmail.com', '0986934918', 'quito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleorden`
--

CREATE TABLE `detalleorden` (
  `detalle_id` int(11) NOT NULL,
  `orden_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `total_linea` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleorden`
--

INSERT INTO `detalleorden` (`detalle_id`, `orden_id`, `producto_id`, `cantidad`, `precio_unitario`, `total_linea`) VALUES
(1, 1, 1, 1, 1.00, 1.00),
(2, 1, 2, 1, 1.00, 1.00),
(3, 2, 3, 20, 20.00, 400.00),
(4, 2, 1, 20, 20.00, 400.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `inventario_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad_disponible` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `orden_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `fecha_orden` date DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`orden_id`, `cliente_id`, `fecha_orden`, `estado`) VALUES
(1, 1, '2025-07-24', 'Pendiente'),
(2, 2, '2025-07-31', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `producto_id` int(11) NOT NULL,
  `nombre_producto` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`producto_id`, `nombre_producto`, `descripcion`, `precio`, `categoria_id`) VALUES
(1, 'Napolitana', 'Napolitana de queso', 10.00, 1),
(2, 'Pan de ambato', 'Pan de ambato', 0.15, 1),
(3, 'Cheesecake', 'pastel', 0.20, 2),
(4, 'Pansito', 'Pansito', 12.00, 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoriasproducto`
--
ALTER TABLE `categoriasproducto`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliente_id`);

--
-- Indices de la tabla `detalleorden`
--
ALTER TABLE `detalleorden`
  ADD PRIMARY KEY (`detalle_id`),
  ADD KEY `orden_id` (`orden_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`inventario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`orden_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`producto_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoriasproducto`
--
ALTER TABLE `categoriasproducto`
  MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalleorden`
--
ALTER TABLE `detalleorden`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `inventario_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `orden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalleorden`
--
ALTER TABLE `detalleorden`
  ADD CONSTRAINT `detalleorden_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`orden_id`),
  ADD CONSTRAINT `detalleorden_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`);

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoriasproducto` (`categoria_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
