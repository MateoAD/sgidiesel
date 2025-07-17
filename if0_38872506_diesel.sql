-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql100.infinityfree.com
-- Tiempo de generación: 16-07-2025 a las 19:51:39
-- Versión del servidor: 10.6.22-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_38872506_diesel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprendices`
--

CREATE TABLE `aprendices` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ficha` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aprendices`
--

INSERT INTO `aprendices` (`id`, `nombre`, `ficha`, `activo`) VALUES
(1, 'admin', '2847431', 1),
(2, 'Santiago Giraldo', '2847431', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` enum('crear','modificar','eliminar','prestamo','devolucion','cambio_estado') NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `detalles` text DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_usuario` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditorias`
--

INSERT INTO `auditorias` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `fecha_accion`, `ip_usuario`) VALUES
(1, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:12:42', '190.251.193.207'),
(2, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:12:46', '181.206.17.234'),
(3, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:12:48', '181.206.17.234'),
(4, 1, 'crear', 'usuarios', 1, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:13:07', '181.206.17.234'),
(5, 1, 'crear', 'usuarios', 1, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:13:13', '181.206.17.234'),
(6, 1, 'crear', 'usuarios', 1, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:13:17', '190.251.193.207'),
(7, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:15:20', '181.206.17.234'),
(8, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:15:33', '190.251.193.207'),
(9, 0, 'crear', 'herramientas_no_consumibles', 0, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"prueba\",\"cantidad\":\"15\"}', '2025-07-16 23:15:47', '181.206.17.234'),
(10, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:16:43', '190.71.97.174'),
(11, 0, 'crear', 'herramientas_consumibles', 1, '{\"descripcion\":\"Nueva herramienta consumible creada\",\"nombre\":\"prueba2\",\"cantidad\":\"100\",\"estado\":\"lleno\"}', '2025-07-16 23:16:46', '181.206.17.234'),
(12, 0, 'crear', 'usuarios', 0, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:21:01', '190.71.97.174'),
(13, 0, 'crear', 'herramientas_consumibles', 2, '{\"descripcion\":\"Nueva herramienta consumible creada\",\"nombre\":\"tuercas\",\"cantidad\":\"45\",\"estado\":\"lleno\"}', '2025-07-16 23:22:01', '190.71.97.174'),
(14, 0, 'crear', 'herramientas_no_consumibles', 0, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"prueba\",\"cantidad\":\"1\"}', '2025-07-16 23:26:51', '181.206.17.234'),
(15, 0, 'crear', 'herramientas_no_consumibles', 1, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"prueba\",\"cantidad\":\"1\"}', '2025-07-16 23:31:40', '181.206.17.234'),
(16, 0, 'eliminar', 'herramientas_no_consumibles', 1, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"prueba\",\"cantidad\":1}', '2025-07-16 23:31:58', '181.206.17.234'),
(17, 0, 'crear', 'herramientas_no_consumibles', 2, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"prueba\",\"cantidad\":\"1\"}', '2025-07-16 23:32:15', '181.206.17.234'),
(18, 0, 'crear', 'herramientas_no_consumibles', 3, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"jrom\",\"cantidad\":\"1\"}', '2025-07-16 23:32:33', '190.71.97.174'),
(19, 0, 'eliminar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"prueba2\",\"cantidad\":100}', '2025-07-16 23:33:26', '181.206.17.234'),
(20, 0, 'crear', 'herramientas_consumibles', 3, '{\"descripcion\":\"Nueva herramienta consumible creada\",\"nombre\":\"prueba4\",\"cantidad\":\"1\",\"estado\":\"lleno\"}', '2025-07-16 23:35:31', '181.206.17.234'),
(21, 2, 'crear', 'usuarios', 2, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:46:40', '181.206.17.234'),
(22, 2, 'crear', 'herramientas_no_consumibles', 4, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"prueba2\",\"cantidad\":\"1\"}', '2025-07-16 23:46:50', '181.206.17.234'),
(23, 2, 'crear', 'usuarios', 2, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-16 23:48:25', '181.206.17.234'),
(24, 2, '', 'reservas_herramientas', 1, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-16 23:48:31', NULL),
(25, 4, 'crear', 'usuarios', 4, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-16 23:49:27', '190.251.193.207'),
(26, 2, '', 'reservas_herramientas', 2, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-16 23:50:23', NULL),
(27, NULL, 'devolucion', 'prestamos', 2, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"jrom\",\"aprendiz\":\"Santiago Giraldo\",\"cantidad\":1,\"tipo\":\"no_consumible\"}', '2025-07-16 23:50:44', '181.206.17.234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajas_herramientas`
--

CREATE TABLE `bajas_herramientas` (
  `id` int(11) NOT NULL,
  `herramienta_id` int(11) NOT NULL,
  `tipo_herramienta` varchar(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `lugar_salida` varchar(100) NOT NULL,
  `lugar_entrada` varchar(100) NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientas_consumibles`
--

CREATE TABLE `herramientas_consumibles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` enum('recargar','medio','lleno') DEFAULT 'lleno',
  `codigo_barras` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientas_consumibles`
--

INSERT INTO `herramientas_consumibles` (`id`, `nombre`, `cantidad`, `estado`, `codigo_barras`, `descripcion`, `ubicacion`, `usuario_id`, `foto`) VALUES
(2, 'tuercas', 38, 'medio', 'CONS_4193A1BD', '', 'Taller', 0, NULL),
(3, 'prueba4', 1, 'recargar', 'CONS_7431824D', 'wedfsfsdf', 'Taller', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientas_no_consumibles`
--

CREATE TABLE `herramientas_no_consumibles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` enum('Activa','Prestada') DEFAULT 'Activa',
  `codigo_barras` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientas_no_consumibles`
--

INSERT INTO `herramientas_no_consumibles` (`id`, `nombre`, `cantidad`, `estado`, `codigo_barras`, `descripcion`, `ubicacion`, `usuario_id`, `foto`) VALUES
(2, 'prueba', 0, 'Activa', 'HERR_67F562BA', 'sdfsdfsdf', 'Taller', 0, NULL),
(3, 'jrom', 1, 'Activa', 'HERR_691C15D4', '', 'Taller', 0, NULL),
(4, 'prueba2', 1, 'Activa', 'HERR_9EACEB16', 'wefwesrwer', 'Taller', 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `id_aprendiz` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `herramienta_id` int(11) NOT NULL,
  `herramienta_tipo` enum('consumible','no_consumible') NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `fecha_prestamo` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_devolucion` timestamp NULL DEFAULT NULL,
  `estado` enum('prestado','devuelto','pendiente','consumida') DEFAULT 'prestado',
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `id_aprendiz`, `usuario_id`, `herramienta_id`, `herramienta_tipo`, `cantidad`, `fecha_prestamo`, `fecha_devolucion`, `estado`, `descripcion`) VALUES
(1, 2, 2, 2, 'consumible', 7, '2025-07-16 23:48:31', NULL, 'consumida', NULL),
(2, 2, 2, 3, 'no_consumible', 1, '2025-07-16 23:50:23', '2025-07-16 23:50:44', 'devuelto', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `id_aprendiz` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `fecha_reporte` datetime NOT NULL,
  `resuelto` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_herramientas`
--

CREATE TABLE `reservas_herramientas` (
  `id` int(11) NOT NULL,
  `herramienta_id` int(11) NOT NULL,
  `tipo_herramienta` enum('consumible','no_consumible') NOT NULL,
  `nombre_aprendiz` varchar(100) NOT NULL,
  `ficha` varchar(20) NOT NULL,
  `fecha_reserva` date NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas_herramientas`
--

INSERT INTO `reservas_herramientas` (`id`, `herramienta_id`, `tipo_herramienta`, `nombre_aprendiz`, `ficha`, `fecha_reserva`, `fecha_solicitud`, `estado`, `cantidad`) VALUES
(1, 2, 'consumible', 'Santiago Giraldo', '2847431', '2025-07-16', '2025-07-16 23:48:18', 'aprobada', 7),
(2, 3, 'no_consumible', 'Santiago Giraldo', '2847431', '2025-07-16', '2025-07-16 23:50:14', 'aprobada', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('administrador','aprendiz','almacenista') NOT NULL DEFAULT 'aprendiz',
  `id_aprendiz` int(11) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `expiracion_token` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contraseña`, `rol`, `id_aprendiz`, `telefono`, `token_recuperacion`, `expiracion_token`, `fecha_creacion`, `activo`) VALUES
(1, 'jromstyle', '$2y$10$9/PKTMmMzFMAxz6i863RL.Ez..W32tLnpM6xT5Lu.ydBlpAI9.Gee', 'aprendiz', NULL, '+573001026551', NULL, NULL, '2025-07-16 23:44:44', 1),
(2, 'admin', '$2y$10$F2CbVAEGfkGBtaGoFvwkkOYpuG9ihCxgUvU8.nGGED0tgCcS1e04a', 'administrador', NULL, '+573014946732', NULL, NULL, '2025-07-16 23:45:36', 1),
(3, 'mateo', '$2y$10$JVfSXincEOBRIltd1z3hP.Su8D8tKbAvRU1dMImyOfmJCb/2gcXgq', 'aprendiz', NULL, '+57300546888', NULL, NULL, '2025-07-16 23:46:45', 1),
(4, 'aprendiz', '$2y$10$XHNTQjN1Hm3lk2pqAQI6fOuvV76bv/xji8ekZXzw4ltuLee6DEhzG', 'aprendiz', NULL, '+573014946732', NULL, NULL, '2025-07-16 23:47:59', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditoria_usuario` (`usuario_id`);

--
-- Indices de la tabla `bajas_herramientas`
--
ALTER TABLE `bajas_herramientas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `herramientas_consumibles`
--
ALTER TABLE `herramientas_consumibles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `fk_hc_usuario` (`usuario_id`);

--
-- Indices de la tabla `herramientas_no_consumibles`
--
ALTER TABLE `herramientas_no_consumibles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `fk_hnc_usuario` (`usuario_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prestamos_aprendiz` (`id_aprendiz`),
  ADD KEY `fk_prestamos_h_no_consumible` (`herramienta_id`),
  ADD KEY `fk_prestamos_usuario` (`usuario_id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aprendiz` (`id_aprendiz`);

--
-- Indices de la tabla `reservas_herramientas`
--
ALTER TABLE `reservas_herramientas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `herramienta_id` (`herramienta_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aprendices`
--
ALTER TABLE `aprendices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `bajas_herramientas`
--
ALTER TABLE `bajas_herramientas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramientas_consumibles`
--
ALTER TABLE `herramientas_consumibles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `herramientas_no_consumibles`
--
ALTER TABLE `herramientas_no_consumibles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas_herramientas`
--
ALTER TABLE `reservas_herramientas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
