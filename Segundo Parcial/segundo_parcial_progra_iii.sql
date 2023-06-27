-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 27-06-2023 a las 02:38:10
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `segundo_parcial_progra_iii`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

CREATE TABLE `monedas` (
  `ID_MONEDA` int(11) NOT NULL,
  `NOMBRE` varchar(30) NOT NULL,
  `SIMBOLO` varchar(10) NOT NULL,
  `NACIONALIDAD` varchar(30) NOT NULL,
  `PRECIO` decimal(20,10) NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `monedas`
--

INSERT INTO `monedas` (`ID_MONEDA`, `NOMBRE`, `SIMBOLO`, `NACIONALIDAD`, `PRECIO`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 'tether', 'usdt', 'estados unidos', '1896.5700000000', '2023-06-26 11:04:48', NULL),
(2, 'bitcoin', 'btc', 'aleman', '30531.1000000000', '2023-06-26 11:06:03', NULL),
(3, 'ethereum', 'eth', 'suizo', '1899.3300000000', '2023-06-26 11:06:53', '2023-06-26 20:53:41'),
(4, 'binance coin', 'bnb', 'suiza', '236.9900000000', '2023-06-26 13:45:18', '2023-06-26 13:46:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_acciones`
--

CREATE TABLE `registro_acciones` (
  `ID_ACCION` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_CRIPTO` int(11) NOT NULL,
  `ACCION` varchar(30) NOT NULL,
  `FECHA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_acciones`
--

INSERT INTO `registro_acciones` (`ID_ACCION`, `ID_USUARIO`, `ID_CRIPTO`, `ACCION`, `FECHA`) VALUES
(1, 1, 3, 'borrar cripto', '2023-06-26 19:27:16'),
(2, 1, 3, 'borrar cripto', '2023-06-26 20:53:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_ingresos`
--

CREATE TABLE `registro_ingresos` (
  `NRO_REGISTRO` int(11) NOT NULL,
  `FECHA_INGRESO` datetime NOT NULL,
  `ID_USUARIO_INGRESADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_ingresos`
--

INSERT INTO `registro_ingresos` (`NRO_REGISTRO`, `FECHA_INGRESO`, `ID_USUARIO_INGRESADO`) VALUES
(1, '2023-06-25 19:25:30', 1),
(2, '2023-06-25 19:26:08', 1),
(3, '2023-06-25 19:35:52', 1),
(4, '2023-06-25 19:38:55', 1),
(5, '2023-06-25 20:09:19', 1),
(6, '2023-06-25 22:22:06', 1),
(7, '2023-06-25 22:28:52', 1),
(8, '2023-06-25 23:05:52', 1),
(9, '2023-06-26 03:39:01', 1),
(10, '2023-06-26 05:35:16', 1),
(11, '2023-06-26 11:02:13', 2),
(12, '2023-06-26 11:03:03', 1),
(13, '2023-06-26 11:08:32', 2),
(14, '2023-06-26 11:10:17', 1),
(15, '2023-06-26 13:36:46', 1),
(16, '2023-06-26 13:49:22', 1),
(17, '2023-06-26 13:51:02', 2),
(18, '2023-06-26 13:52:05', 1),
(19, '2023-06-26 19:24:25', 1),
(20, '2023-06-26 20:54:37', 2),
(21, '2023-06-26 20:55:23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_USUARIO` int(11) NOT NULL,
  `NOMBRE` varchar(30) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `PASSWORD` varchar(100) NOT NULL,
  `TIPO` varchar(8) NOT NULL,
  `FECHA_ALTA` datetime NOT NULL,
  `FECHA_BAJA` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_USUARIO`, `NOMBRE`, `EMAIL`, `PASSWORD`, `TIPO`, `FECHA_ALTA`, `FECHA_BAJA`) VALUES
(1, 'Emanuel', 'emanueltrotta@gmail.com', '$2y$10$9xRC/q1lSdml0ndxAJB9.OhOrAWHIcMB9Zps9daqdB7qsnFKlvR6q', 'admin', '2023-06-25 19:15:56', NULL),
(2, 'Tatiana', 'tatiana@gmail.com', '$2y$10$Q9D3lGKLrpPyNh9GlUDeAu3LW3zzJnw0fywTUwOkbJYk93EiGzs2m', 'cliente', '2023-06-25 20:10:22', NULL),
(3, 'Elias', 'elias@gmail.com', '$2y$10$IrdYrSQdYn5zzQeSiiM1C.0B7MoqJ74w.ibC89wcRZcgmOib1yuxS', 'cliente', '2023-06-26 13:37:03', '2023-06-26 13:39:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `ID_VENTA` int(11) NOT NULL,
  `ID_MONEDA` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `PRECIO` decimal(20,10) NOT NULL,
  `CANTIDAD` int(11) NOT NULL,
  `TOTAL` decimal(20,10) NOT NULL,
  `FECHA` datetime NOT NULL,
  `ESTADO` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`ID_VENTA`, `ID_MONEDA`, `ID_USUARIO`, `PRECIO`, `CANTIDAD`, `TOTAL`, `FECHA`, `ESTADO`) VALUES
(1, 1, 1, '1896.5700000000', 10, '18965.7000000000', '2023-06-25 00:00:00', 'cancelada'),
(2, 2, 2, '30531.1000000000', 5, '152655.5000000000', '2023-06-26 00:00:00', 'cerrada'),
(3, 2, 1, '30531.1000000000', 2, '61062.2000000000', '2023-06-26 11:10:57', 'cerrada'),
(4, 2, 1, '30531.1000000000', 2, '61062.2000000000', '2023-06-24 11:12:52', 'cerrada'),
(5, 3, 1, '1899.3300000000', 2, '3798.6600000000', '2023-06-26 12:30:54', 'cerrada');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `monedas`
--
ALTER TABLE `monedas`
  ADD PRIMARY KEY (`ID_MONEDA`);

--
-- Indices de la tabla `registro_acciones`
--
ALTER TABLE `registro_acciones`
  ADD PRIMARY KEY (`ID_ACCION`);

--
-- Indices de la tabla `registro_ingresos`
--
ALTER TABLE `registro_ingresos`
  ADD PRIMARY KEY (`NRO_REGISTRO`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_USUARIO`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`ID_VENTA`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `monedas`
--
ALTER TABLE `monedas`
  MODIFY `ID_MONEDA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `registro_acciones`
--
ALTER TABLE `registro_acciones`
  MODIFY `ID_ACCION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `registro_ingresos`
--
ALTER TABLE `registro_ingresos`
  MODIFY `NRO_REGISTRO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `ID_VENTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
