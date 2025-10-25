-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2025 a las 17:39:18
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
-- Base de datos: `bdprueba`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobranza`
--

CREATE TABLE `cobranza` (
  `Id_codigo` int(11) NOT NULL,
  `Concepto` varchar(255) NOT NULL,
  `Fecha_hora` datetime NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_unitario` decimal(12,2) NOT NULL,
  `Total` decimal(12,2) NOT NULL,
  `Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cobranza`
--

INSERT INTO `cobranza` (`Id_codigo`, `Concepto`, `Fecha_hora`, `Cantidad`, `Precio_unitario`, `Total`, `Id`) VALUES
(102, 'Televisor 32\"', '2025-10-02 12:15:00', 2, 650.00, 3000.00, 1),
(102, 'Equipo de sonido', '2025-10-02 12:16:00', 1, 1500.00, 1500.00, 2),
(102, 'Microondas', '2025-10-02 14:45:00', 2, 450.00, 900.00, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `Id_codigo` int(11) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Nickname` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`Id_codigo`, `Apellido`, `Nombre`, `Nickname`, `Password`, `Rol`) VALUES
(101, 'Cuentas', 'Omar', 'user1', '12345', 'Soporte'),
(102, 'Gallegos', 'Nestor', 'user2', '1234', 'Caja'),
(103, 'Neira', 'Ruben', 'admin', '123456', 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `producto` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `cliente`, `producto`, `cantidad`, `precio`, `total`) VALUES
(1, '2025-10-16', 'Cliente A', 'Servicio A', 2, 50.00, 100.00),
(2, '2025-10-15', 'Cliente B', 'Servicio B', 1, 80.00, 80.00),
(3, '2025-10-17', 'Cliente C', 'Servicio A', 3, 50.00, 150.00),
(4, '2025-10-17', 'Cliente D', 'Servicio C', 5, 20.00, 100.00),
(5, '2025-10-11', 'Cliente E', 'Servicio B', 4, 80.00, 320.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cobranza`
--
ALTER TABLE `cobranza`
  ADD PRIMARY KEY (`Id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Id_codigo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cobranza`
--
ALTER TABLE `cobranza`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
