-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2025 a las 18:37:32
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
-- Base de datos: `ferreteria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(30) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `dni`, `telefono`, `direccion`) VALUES
(1, 'Edward', '71501064', '987654321', 'Cusco'),
(2, 'Juan', '75421625', '965478123', 'Lima'),
(3, 'Juan Quiroz', '25364189', '956874123', 'Lima'),
(4, 'María Torres', '78451236', '987654322', 'Arequipa'),
(5, 'Carlos Quispe', '70521347', '956874125', 'Puno'),
(6, 'Lucía Ramos', '75489632', '944556678', 'Tacna'),
(7, 'Diego Castro', '74582136', '945678912', 'Cusco'),
(8, 'Patricia López', '75231489', '932145789', 'Juliaca'),
(9, 'Andrés Pérez', '76147852', '987456321', 'Lima'),
(10, 'Rosa Gutiérrez', '78521463', '963258741', 'Arequipa'),
(11, 'José Vargas', '74125896', '974563214', 'Moquegua'),
(12, 'Sandra Flores', '79631425', '987451236', 'Ilo'),
(13, 'Miguel Paredes', '73125489', '954126378', 'Lima'),
(14, 'Diana Mendoza', '72456132', '998745612', 'Cusco'),
(15, 'Héctor Valdez', '71326548', '987452316', 'Puno'),
(16, 'Alejandra Ramos', '75896412', '963147852', 'Tacna'),
(17, 'Roberto Nina', '76458921', '987654987', 'Arequipa'),
(18, 'Elena Quispe', '74123654', '912345678', 'Juliaca'),
(19, 'Pablo Huamán', '73216548', '956789123', 'Cusco');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `subtotal`) VALUES
(1, 1, 3, 1, 2500.00),
(2, 2, 2, 6, 36.00),
(3, 3, 3, 2, 5000.00),
(4, 4, 4, 2, 320.00),
(5, 5, NULL, 1, 40.00),
(6, 6, 4, 18, 2880.00),
(7, 7, NULL, 2, 80.00),
(9, 9, 5, 1, 150.00),
(10, 10, 6, 5, 175.00),
(11, 11, 7, 3, 135.00),
(12, 12, 8, 2, 80.00),
(13, 13, 9, 5, 110.00),
(14, 14, 10, 7, 140.00),
(15, 15, 11, 4, 200.00),
(16, 16, 12, 6, 240.00),
(17, 17, 13, 10, 150.00),
(18, 18, 14, 3, 84.00),
(19, 19, 15, 8, 96.00),
(20, 20, 16, 5, 50.00),
(21, 21, 17, 2, 70.00),
(22, 22, 18, 1, 250.00),
(23, 23, 19, 2, 840.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `proveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `categoria`, `precio_compra`, `precio_venta`, `stock`, `proveedor_id`) VALUES
(2, 'Tijeras', '', 'Cortadoras', 5.00, 6.00, 44, 1),
(3, 'Cafetera', 'Cafetera 2L', 'Electrodomesticos', 50.00, 65.00, 17, 1),
(4, 'Sierra Electrica', 'Sierra Electrica 2530', 'Electrodomesticos', 150.00, 160.00, 0, 1),
(5, 'Taladro', 'Taladro eléctrico 500W', 'Herramientas', 120.00, 150.00, 25, 3),
(6, 'Martillo', 'Martillo de acero inoxidable', 'Herramientas', 25.00, 35.00, 100, 2),
(7, 'Destornillador', 'Set de 6 piezas', 'Herramientas', 30.00, 45.00, 80, 2),
(8, 'Alicate', 'Alicate de presión reforzado', 'Herramientas', 28.00, 40.00, 75, 3),
(9, 'Cable eléctrico', 'Rollo de 20 metros', 'Eléctricos', 15.00, 22.00, 90, 5),
(10, 'Foco LED 20W', 'Foco de bajo consumo', 'Iluminación', 12.00, 20.00, 150, 6),
(11, 'Pintura Blanca', 'Galón 4L', 'Pinturas', 35.00, 50.00, 60, 4),
(12, 'Cemento Portland', 'Bolsa 42.5kg', 'Construcción', 28.00, 40.00, 100, 7),
(13, 'Tornillos', 'Caja de 100 unidades', 'Fijaciones', 10.00, 15.00, 200, 8),
(14, 'Llave inglesa', 'Llave ajustable 10"', 'Herramientas', 18.00, 28.00, 70, 9),
(15, 'Tubos PVC', 'Tubo de 2 metros', 'Plomería', 8.00, 12.00, 150, 10),
(16, 'Guantes de seguridad', 'Par resistente', 'Seguridad', 6.00, 10.00, 120, 11),
(17, 'Mascarilla protectora', 'Caja de 50 unidades', 'Seguridad', 20.00, 35.00, 60, 12),
(18, 'Escalera de aluminio', 'Escalera 3 metros', 'Equipos', 200.00, 250.00, 15, 13),
(19, 'Soldadora eléctrica', 'Soldadora 220V', 'Equipos', 350.00, 420.00, 10, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--


-- --------------------------------------------------------

--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_cliente`, `id_usuario`, `fecha`, `total`) VALUES
(1, 1, NULL, '2025-10-08 13:05:07', 2500.00),
(2, 1, 1, '2025-10-08 13:45:51', 36.00),
(3, 2, 1, '2025-10-08 13:46:24', 5000.00),
(4, 3, 1, '2025-10-08 14:13:33', 320.00),
(5, 1, 1, '2025-10-08 14:20:27', 40.00),
(6, 2, 1, '2025-10-08 14:21:47', 2880.00),
(7, 3, 3, '2025-10-08 14:26:27', 80.00),
(8, 1, 1, '2025-10-15 17:02:25', 3397978560.00),
(9, 4, 1, '2025-10-09 09:10:00', 150.00),
(10, 5, 3, '2025-10-09 09:45:00', 250.00),
(11, 6, 3, '2025-10-09 10:00:00', 350.00),
(12, 7, 1, '2025-10-09 10:30:00', 120.00),
(13, 8, 1, '2025-10-09 11:00:00', 95.00),
(14, 9, 3, '2025-10-09 11:45:00', 420.00),
(15, 10, 3, '2025-10-09 12:00:00', 280.00),
(16, 11, 1, '2025-10-09 12:30:00', 180.00),
(17, 12, 1, '2025-10-09 13:00:00', 350.00),
(18, 13, 3, '2025-10-09 13:45:00', 90.00),
(19, 14, 3, '2025-10-09 14:00:00', 175.00),
(20, 15, 1, '2025-10-09 14:30:00', 420.00),
(21, 16, 1, '2025-10-09 15:00:00', 210.00),
(22, 17, 3, '2025-10-09 15:30:00', 600.00),
(23, 18, 3, '2025-10-09 16:00:00', 95.00);

--
-- Índices para tablas volcadas
--

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `dni` (`dni`);

ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `proveedor_id` (`proveedor_id`);

ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `detalle_venta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

COMMIT;
