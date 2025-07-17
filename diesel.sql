-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-07-2025 a las 20:43:38
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
-- Base de datos: `diesel`
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
(1, 'mateo arboleda diaz', '2847431', 0),
(2, 'jeronimo sanchez holgin', '2847411', 1),
(3, 'martin perez gonzales', '55545454', 1),
(4, 'benito camelo suave', '55545454', 0),
(5, 'susanita porras', '55545454', 0),
(6, 'lina gonzales', '55545454', 1),
(7, 'lola perez', '2847431', 1),
(8, 'LOLA PEREZ', '2847431', 1),
(9, 'JUNITOS PARRA', '2847431', 1),
(10, 'SANTIAGO GIRTALOD', '2847431', 0),
(11, 'mateo loaiza', '323212312321', 0),
(12, 'carlos gil', '323212312321', 0),
(13, 'carlos1', '123456', 1),
(14, 'carlos2', '123456', 1),
(15, 'carlos3', '123456', 1),
(16, 'carlos4', '123456', 1),
(17, 'carlos5', '123456', 1),
(18, 'carlos6', '123456', 1),
(19, 'carlos7', '123456', 1),
(20, 'carlos8', '123456', 1),
(21, 'carlos9', '123456', 1),
(22, 'carlos10', '123456', 1),
(23, 'carlos11', '123456', 1),
(24, 'carlos12', '123456', 1),
(25, 'carlos13', '123456', 1),
(26, 'carlos14', '123456', 1),
(27, 'carlos15', '123456', 1),
(28, 'invi', '2847431', 1),
(29, 'luis', '2847431', 1),
(30, 'alan brito', '2847431', 0),
(31, 'natalia cardona', '2847431', 1),
(32, 'carlos1', '123456', 1),
(33, 'carlos2', '123456', 1),
(34, 'carlos3', '123456', 1),
(35, 'carlos4', '123456', 1),
(36, 'carlos5', '123456', 1),
(37, 'carlos6', '123456', 1),
(38, 'carlos7', '123456', 1),
(39, 'carlos8', '123456', 1),
(40, 'carlos9', '123456', 1),
(41, 'carlos10', '123456', 1),
(42, 'carlos11', '123456', 1),
(43, 'carlos12', '123456', 1),
(44, 'carlos13', '123456', 1),
(45, 'carlos14', '123456', 1),
(46, 'carlos15', '123456', 1),
(47, 'mateo arboleda ', '2847431', 1),
(48, 'jhon', '2847431', 1),
(49, 'julio jaramilo', '2847431', 1),
(50, 'jromstyle', '2847431', 1),
(51, 'san', '2847431', 1),
(52, 'Santiago Giraldo', '2847431', 1);

--
-- Disparadores `aprendices`
--
DELIMITER $$
CREATE TRIGGER `after_aprendices_delete` AFTER DELETE ON `aprendices` FOR EACH ROW BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'eliminar',
            'aprendices',
            OLD.id,
            JSON_OBJECT(
                'descripcion', 'Aprendiz eliminado del sistema',
                'nombre', OLD.nombre,
                'ficha', OLD.ficha
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;

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
(20, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-23 22:19:30', '::1'),
(21, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-23 22:49:54', '::1'),
(22, NULL, 'crear', 'usuarios', 8, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"espinaca\"}', '2025-04-23 23:00:37', '::1'),
(23, NULL, 'prestamo', 'prestamos', 51, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 4, \"tipo\": \"no_consumible\"}', '2025-04-23 23:01:07', NULL),
(25, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-23 23:02:22', '::1'),
(27, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 03:20:37', '::1'),
(28, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 03:20:39', '::1'),
(29, NULL, 'crear', 'usuarios', 8, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"espinaca\"}', '2025-04-24 03:26:20', '::1'),
(30, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 03:27:09', '::1'),
(34, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 12:41:43', '::1'),
(38, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 13:16:33', '::1'),
(40, NULL, 'prestamo', 'prestamos', 52, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-24 13:27:41', NULL),
(42, NULL, 'prestamo', 'prestamos', 53, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 50, \"tipo\": \"consumible\"}', '2025-04-24 13:29:13', NULL),
(44, NULL, 'crear', 'usuarios', 9, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"alberto\"}', '2025-04-24 13:45:53', '::1'),
(45, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 13:46:53', '::1'),
(54, NULL, 'crear', 'usuarios', 9, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"alberto\"}', '2025-04-24 13:54:30', '::1'),
(59, NULL, 'prestamo', 'prestamos', 60, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-24 14:02:54', NULL),
(60, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 13, \"cantidad_nueva\": 8, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-24 14:02:55', NULL),
(61, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 14:03:10', '::1'),
(66, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 50, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}', '2025-04-24 14:12:20', NULL),
(67, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 200, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}', '2025-04-24 14:13:32', NULL),
(68, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":50,\"cantidad_nueva\":\"200\",\"estado_anterior\":\"medio\",\"estado_nuevo\":\"medio\"}', '2025-04-24 14:13:32', '::1'),
(69, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 14:13:33', NULL),
(70, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 14:13:58', '::1'),
(71, NULL, 'crear', 'herramientas_no_consumibles', 15, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"pala\", \"cantidad\": 30, \"estado\": \"\"}', '2025-04-24 14:18:05', NULL),
(72, NULL, 'crear', 'herramientas_consumibles', 6, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"puntilla\", \"cantidad\": 100, \"estado\": \"lleno\"}', '2025-04-24 14:18:30', NULL),
(73, NULL, 'crear', 'herramientas_consumibles', 7, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"aceite\", \"cantidad\": 20, \"estado\": \"lleno\"}', '2025-04-24 14:18:40', NULL),
(74, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 14:18:42', NULL),
(79, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 14:25:42', '::1'),
(80, 10, 'eliminar', 'herramientas_consumibles', 6, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"puntilla\",\"cantidad\":100}', '2025-04-24 14:25:56', '::1'),
(81, 10, 'eliminar', 'herramientas_consumibles', 6, '{\"descripcion\": \"Herramienta consumible eliminada\", \"nombre\": \"puntilla\", \"cantidad\": 100}', '2025-04-24 14:25:56', NULL),
(82, 10, 'crear', 'herramientas_no_consumibles', 16, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"llave 8\", \"cantidad\": 50, \"estado\": \"\"}', '2025-04-24 14:36:43', NULL),
(83, 10, 'eliminar', 'herramientas_no_consumibles', 15, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"pala\",\"cantidad\":30}', '2025-04-24 14:37:12', '::1'),
(84, 10, 'eliminar', 'herramientas_no_consumibles', 15, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"pala\", \"cantidad\": 30}', '2025-04-24 14:37:12', NULL),
(85, 10, 'crear', 'herramientas_no_consumibles', 17, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"rosas\", \"cantidad\": 21, \"estado\": \"\"}', '2025-04-24 14:44:00', NULL),
(86, 10, 'eliminar', 'herramientas_no_consumibles', 17, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"rosas\",\"cantidad\":21}', '2025-04-24 14:44:38', '::1'),
(87, 10, 'eliminar', 'herramientas_no_consumibles', 17, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"rosas\", \"cantidad\": 21}', '2025-04-24 14:44:38', NULL),
(88, 10, 'crear', 'herramientas_no_consumibles', 18, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"luoa\", \"cantidad\": 23, \"estado\": \"\"}', '2025-04-24 14:56:56', NULL),
(89, 10, 'modificar', 'herramientas_no_consumibles', 18, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"luoa\", \"cantidad_anterior\": 23, \"cantidad_nueva\": 244, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 14:57:11', NULL),
(90, 10, 'modificar', 'herramientas_no_consumibles', 18, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"luoa\",\"cantidad_anterior\":23,\"cantidad_nueva\":\"244\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 14:57:11', '::1'),
(91, 10, 'eliminar', 'herramientas_no_consumibles', 18, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"luoa\",\"cantidad\":244}', '2025-04-24 14:57:18', '::1'),
(92, 10, 'crear', 'herramientas_no_consumibles', 19, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"sds\", \"cantidad\": 2, \"estado\": \"\"}', '2025-04-24 15:04:17', NULL),
(93, 10, 'modificar', 'herramientas_no_consumibles', 19, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2, \"cantidad_nueva\": 222, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:04:25', NULL),
(94, 10, 'modificar', 'herramientas_no_consumibles', 19, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2, \"cantidad_nueva\": 222, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:04:25', NULL),
(95, 10, 'modificar', 'herramientas_no_consumibles', 19, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"sds\",\"cantidad_anterior\":2,\"cantidad_nueva\":\"222\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 15:04:25', '::1'),
(96, 10, 'eliminar', 'herramientas_no_consumibles', 19, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"sds\",\"cantidad\":222}', '2025-04-24 15:04:31', '::1'),
(97, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:04:35', NULL),
(98, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:04:35', NULL),
(99, 10, 'crear', 'herramientas_consumibles', 8, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"sds\", \"cantidad\": 22, \"estado\": \"lleno\"}', '2025-04-24 15:04:46', NULL),
(100, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:04:47', NULL),
(101, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:04:47', NULL),
(102, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 22, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}', '2025-04-24 15:04:47', NULL),
(103, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 22, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}', '2025-04-24 15:04:47', NULL),
(104, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}', '2025-04-24 15:04:53', NULL),
(105, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}', '2025-04-24 15:04:53', NULL),
(106, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"sds\",\"cantidad_anterior\":22,\"cantidad_nueva\":\"2222\",\"estado_anterior\":\"medio\",\"estado_nuevo\":\"medio\"}', '2025-04-24 15:04:53', '::1'),
(107, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:04:55', NULL),
(108, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:04:55', NULL),
(109, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2222, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:04:55', NULL),
(110, 10, 'modificar', 'herramientas_consumibles', 8, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2222, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:04:55', NULL),
(111, 10, 'eliminar', 'herramientas_consumibles', 8, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"sds\",\"cantidad\":2222}', '2025-04-24 15:04:59', '::1'),
(112, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:05:00', NULL),
(113, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:05:00', NULL),
(114, 10, 'crear', 'herramientas_no_consumibles', 20, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"lllave\", \"cantidad\": 20, \"estado\": \"\"}', '2025-04-24 15:14:39', NULL),
(115, 10, 'modificar', 'herramientas_no_consumibles', 20, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"lllave\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 21, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:15:06', NULL),
(116, 10, 'modificar', 'herramientas_no_consumibles', 20, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"lllave\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 21, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:15:06', NULL),
(117, 10, 'modificar', 'herramientas_no_consumibles', 20, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"lllave\",\"cantidad_anterior\":20,\"cantidad_nueva\":\"21\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 15:15:06', '::1'),
(118, 10, 'eliminar', 'herramientas_no_consumibles', 20, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"lllave\",\"cantidad\":21}', '2025-04-24 15:15:46', '::1'),
(119, 10, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 77, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:24:59', NULL),
(120, 10, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"llave 8\",\"cantidad_anterior\":50,\"cantidad_nueva\":\"77\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 15:24:59', '::1'),
(121, 10, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 77, \"cantidad_nueva\": 50, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-04-24 15:36:29', NULL),
(122, 10, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"llave 8\",\"cantidad_anterior\":77,\"cantidad_nueva\":\"50\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 15:36:29', '::1'),
(123, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:41:40', NULL),
(124, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:41:40', NULL),
(125, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 15:41:50', NULL),
(126, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"aceite\",\"cantidad_anterior\":20,\"cantidad_nueva\":\"100\",\"estado_anterior\":\"recargar\",\"estado_nuevo\":\"recargar\"}', '2025-04-24 15:41:50', '::1'),
(127, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:41:51', NULL),
(128, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:41:51', NULL),
(129, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:53:15', NULL),
(130, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:53:15', NULL),
(131, 10, 'prestamo', 'prestamos', 61, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 100, \"tipo\": \"consumible\"}', '2025-04-24 15:54:01', NULL),
(132, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:54:01', NULL),
(133, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:54:11', NULL),
(134, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:54:11', NULL),
(135, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:55:32', NULL),
(136, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":100,\"cantidad_nueva\":\"1000\",\"estado_anterior\":\"lleno\",\"estado_nuevo\":\"lleno\"}', '2025-04-24 15:55:32', '::1'),
(137, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:55:34', NULL),
(138, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 15:55:34', NULL),
(139, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 16:16:34', NULL),
(140, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 16:16:34', NULL),
(141, 10, 'crear', 'herramientas_no_consumibles', 21, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"lupa\", \"cantidad\": 15, \"estado\": \"\"}', '2025-04-24 16:17:01', NULL),
(142, 10, 'modificar', 'herramientas_no_consumibles', 21, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"lupa\",\"cantidad_anterior\":15,\"cantidad_nueva\":20,\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}', '2025-04-24 16:17:28', '::1'),
(143, 10, 'eliminar', 'herramientas_no_consumibles', 21, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"lupa\",\"cantidad\":20}', '2025-04-24 16:17:52', '::1'),
(144, 10, 'prestamo', 'prestamos', 62, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 233, \"tipo\": \"consumible\"}', '2025-04-24 16:19:31', NULL),
(145, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 767, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 16:19:31', NULL),
(146, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 17:01:01', '::1'),
(152, NULL, 'modificar', 'usuarios', 7, '{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}', '2025-04-24 17:06:19', NULL),
(153, NULL, 'modificar', 'usuarios', 7, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario_id\":7,\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}', '2025-04-24 17:06:19', '::1'),
(154, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 17:06:57', '::1'),
(173, NULL, 'crear', 'usuarios', 7, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-04-24 17:45:50', '::1'),
(174, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"martillo\",\"cantidad_anterior\":17,\"cantidad_nueva\":27,\"estado_anterior\":\"Activa\",\"estado_nuevo\":\"Activa\"}', '2025-04-24 17:46:10', '::1'),
(175, NULL, 'prestamo', 'prestamos', 63, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-04-24 17:48:06', NULL),
(176, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 24, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-24 17:48:06', NULL),
(177, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 411, \"cantidad_nueva\": 411, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:50:09', NULL),
(178, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:50:09', NULL),
(179, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":411,\"cantidad_nueva\":41,\"estado_anterior\":\"lleno\",\"estado_nuevo\":\"lleno\"}', '2025-04-24 17:50:16', '::1'),
(180, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}', '2025-04-24 17:50:17', NULL),
(181, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:50:17', NULL),
(182, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}', '2025-04-24 17:57:19', NULL),
(183, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:57:19', NULL),
(184, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}', '2025-04-24 17:59:12', NULL),
(185, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:59:12', NULL),
(186, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":41,\"nuevo\":\"10\"}}}', '2025-04-24 17:59:20', '::1'),
(187, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 17:59:21', NULL),
(188, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 17:59:21', NULL),
(189, NULL, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\":\"Modificaci\\u00f3n de herramienta\",\"nombre\":\"llave 8\",\"cambios\":{\"cantidad\":{\"anterior\":50,\"nuevo\":\"10\"}}}', '2025-04-24 18:00:12', '::1'),
(190, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 18:08:24', NULL),
(191, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:08:24', NULL),
(192, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":10,\"nuevo\":\"100\"}}}', '2025-04-24 18:08:32', '::1'),
(193, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:08:34', NULL),
(194, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:08:34', NULL),
(195, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:13', NULL),
(196, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:13', NULL),
(197, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:20', NULL),
(198, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:20', NULL),
(199, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:37', NULL),
(200, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:37', NULL),
(201, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":100,\"nuevo\":\"10\"}}}', '2025-04-24 18:13:44', '::1'),
(202, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 18:13:45', NULL),
(203, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:13:45', NULL),
(204, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 18:17:14', NULL),
(205, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:17:14', NULL),
(206, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":10,\"nuevo\":\"100\"}}}', '2025-04-24 18:17:25', '::1'),
(207, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:17:26', NULL),
(208, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:17:26', NULL),
(209, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:29:13', NULL),
(210, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}', '2025-04-24 18:29:15', NULL),
(211, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}', '2025-04-24 18:29:15', NULL),
(212, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}', '2025-04-24 18:48:08', NULL),
(213, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:48:08', NULL),
(214, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}', '2025-04-24 18:48:18', NULL),
(215, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:48:20', NULL),
(216, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:48:20', NULL),
(217, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:55:50', NULL),
(218, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:55:50', NULL),
(219, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:56:00', NULL),
(220, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":101,\"nuevo\":\"10\"}}}', '2025-04-24 18:56:00', '::1'),
(221, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}}', '2025-04-24 18:56:01', NULL),
(222, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 18:56:01', NULL),
(223, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}', '2025-04-24 19:07:37', NULL),
(224, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 19:07:37', NULL),
(225, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}', '2025-04-24 19:07:54', NULL),
(226, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cantidad_anterior\":10,\"cantidad_nueva\":\"101\"}', '2025-04-24 19:07:54', '::1'),
(227, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 19:07:55', NULL),
(228, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}', '2025-04-24 19:07:55', NULL),
(229, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 5}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-24 19:13:38', NULL),
(230, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 5, \"nuevo\": 5}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-24 19:13:39', NULL),
(231, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 5, \"nuevo\": 66}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-24 19:17:47', NULL),
(232, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 66, \"nuevo\": 66}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-24 19:17:48', NULL),
(233, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 6, \"nuevo\": 6}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-24 19:24:55', NULL),
(234, NULL, 'crear', 'herramientas_consumibles', 11, '{\"descripcion\":\"Creaci\\u00f3n de material consumible\",\"nombre\":\"dsad\",\"cantidad\":\"12\",\"estado\":\"lleno\"}', '2025-04-24 19:48:34', '::1'),
(235, NULL, 'modificar', 'herramientas_consumibles', 11, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"dsad\", \"cambios\": {\"cantidad\": {\"anterior\": 12, \"nuevo\": 12}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-04-24 19:48:35', NULL),
(236, NULL, 'crear', 'herramientas_no_consumibles', 22, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"sad\",\"cantidad\":\"12\"}', '2025-04-24 19:48:46', '::1'),
(237, NULL, 'eliminar', 'herramientas_no_consumibles', 22, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"sad\",\"cantidad\":12}', '2025-04-24 19:48:56', '::1'),
(239, NULL, 'devolucion', 'prestamos', 60, '{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-04-24 20:01:25', '::1'),
(241, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:20:47', '::1'),
(242, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:22:18', '::1'),
(243, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:29:42', '::1'),
(244, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:33:14', '::1'),
(245, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:35:23', '::1'),
(246, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-24 21:52:18', '::1'),
(247, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-26 15:10:42', '::1'),
(248, 10, 'modificar', 'usuarios', 7, '{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}', '2025-04-26 15:15:31', NULL),
(249, 10, 'modificar', 'usuarios', 7, '{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}', '2025-04-26 15:16:09', NULL),
(250, 10, 'modificar', 'usuarios', 7, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"mateo\",\"rol_anterior\":\"usuario\",\"rol_nuevo\":\"administrador\"}', '2025-04-26 15:20:41', '::1'),
(251, 10, 'eliminar', 'usuarios', 11, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}', '2025-04-26 15:20:50', '::1'),
(253, 10, 'prestamo', 'prestamos', 64, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-04-26 15:23:50', NULL),
(254, 10, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 29, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 15:23:50', NULL),
(256, 10, 'devolucion', 'prestamos', 64, '{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-04-26 15:24:22', '::1'),
(258, 10, 'devolucion', 'prestamos', 63, '{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-04-26 15:24:26', '::1'),
(259, NULL, 'crear', 'usuarios', 12, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-04-26 15:34:25', '::1'),
(263, 10, 'eliminar', 'usuarios', 12, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}', '2025-04-26 15:51:34', '::1'),
(267, 10, 'modificar', 'usuarios', 5, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"carlos loaiza\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}', '2025-04-26 15:52:57', '::1'),
(273, 10, 'eliminar', 'usuarios', 9, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"alberto\",\"rol\":\"usuario\"}', '2025-04-26 15:56:45', '::1'),
(276, 10, 'eliminar', 'usuarios', 7, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"mateo\",\"rol\":\"administrador\"}', '2025-04-26 15:56:59', '::1'),
(282, 10, 'eliminar', 'usuarios', 8, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"espinaca\",\"rol\":\"usuario\"}', '2025-04-26 15:59:01', '::1'),
(311, 10, 'eliminar', 'usuarios', 5, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"carlos loaiza\",\"rol\":\"usuario\"}', '2025-04-26 16:18:19', '::1'),
(312, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 16:18:19', NULL),
(313, 5, 'eliminar', 'usuarios', 5, '{\"descripcion\": \"Eliminación de usuario\", \"usuario\": \"carlos loaiza\", \"rol\": \"usuario\"}', '2025-04-26 16:18:19', NULL),
(314, 10, 'modificar', 'usuarios', 13, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"usuario\",\"rol_nuevo\":\"administrador\"}', '2025-04-26 16:21:23', '::1'),
(315, 10, 'modificar', 'usuarios', 13, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}', '2025-04-26 16:21:27', '::1'),
(316, 10, 'eliminar', 'usuarios', 13, '{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}', '2025-04-26 16:21:43', '::1'),
(317, 5, 'eliminar', 'usuarios', 13, '{\"descripcion\": \"Eliminación de usuario\", \"usuario\": \"aprendiz\", \"rol\": \"usuario\"}', '2025-04-26 16:21:43', NULL),
(318, 14, 'prestamo', 'prestamos', 65, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-26 16:25:20', NULL),
(319, 14, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 16:25:20', NULL),
(332, 14, 'devolucion', 'prestamos', 65, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-26 17:09:16', NULL),
(333, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 17:09:16', NULL),
(334, NULL, 'devolucion', 'prestamos', 65, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-04-26 17:09:16', '::1'),
(335, 10, 'prestamo', 'prestamos', 66, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-26 17:10:35', NULL),
(336, 10, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 17:10:35', NULL),
(337, 10, 'devolucion', 'prestamos', 66, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-04-26 17:11:16', NULL),
(338, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-26 17:11:16', NULL),
(339, NULL, 'devolucion', 'prestamos', 66, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-04-26 17:11:16', '::1'),
(340, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-29 12:33:03', '::1'),
(341, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-30 15:23:09', '::1');
INSERT INTO `auditorias` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `fecha_accion`, `ip_usuario`) VALUES
(342, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-30 15:26:05', '::1'),
(343, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-04-30 15:38:24', '::1'),
(344, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-04-30 16:07:35', '::1'),
(345, 10, 'prestamo', 'prestamos', 67, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-04-30 16:09:19', NULL),
(346, 10, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 30, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-30 16:09:19', NULL),
(347, 10, 'devolucion', 'prestamos', 67, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-04-30 16:09:44', NULL),
(348, NULL, 'modificar', 'herramientas_no_consumibles', 5, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 30, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}', '2025-04-30 16:09:44', NULL),
(349, NULL, 'devolucion', 'prestamos', 67, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-04-30 16:09:44', '::1'),
(350, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-04-30 16:10:33', NULL),
(351, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-04-30 16:11:44', '::1'),
(352, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 12:46:54', '::1'),
(353, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 12:46:59', '::1'),
(354, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 13:32:49', '10.2.22.76'),
(355, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 13:33:39', '10.2.26.233'),
(356, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 13:34:13', '10.2.26.233'),
(357, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 13:49:54', '::1'),
(358, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 14:11:14', '10.2.30.234'),
(359, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 14:11:19', '10.2.30.234'),
(360, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 14:17:05', '::1'),
(361, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 14:29:15', '10.2.26.233'),
(362, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 15:32:08', '::1'),
(363, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-12 15:32:09', '::1'),
(364, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-12 15:40:13', '::1'),
(365, NULL, 'modificar', 'herramientas_no_consumibles', 16, '{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}', '2025-05-12 16:12:11', NULL),
(366, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-13 12:36:10', '::1'),
(367, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-13 12:57:43', '::1'),
(368, 10, 'modificar', 'herramientas_consumibles', 11, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"dsad\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-05-13 13:09:21', NULL),
(369, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-14 15:02:42', '::1'),
(370, 10, 'crear', 'herramientas_no_consumibles', 23, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"torquimetro\",\"cantidad\":\"50\"}', '2025-05-14 15:04:17', '::1'),
(371, 10, 'eliminar', 'herramientas_no_consumibles', 23, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"torquimetro\",\"cantidad\":50}', '2025-05-14 15:08:32', '::1'),
(372, 10, 'eliminar', 'herramientas_consumibles', 11, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"dsad\",\"cantidad\":100}', '2025-05-14 15:08:58', '::1'),
(373, 10, 'crear', 'herramientas_no_consumibles', 27, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"compresimetro\",\"cantidad\":\"45\",\"codigo_barras\":\"HERR_5D7D9B05\"}', '2025-05-14 15:25:11', '::1'),
(374, 10, 'crear', 'herramientas_consumibles', 12, '{\"descripcion\":\"Creaci\\u00f3n de material consumible\",\"nombre\":\"tuercas\",\"cantidad\":\"100\",\"codigo_barras\":\"CONS_61DC8A46\",\"estado\":\"lleno\"}', '2025-05-14 15:26:21', '::1'),
(375, 10, 'crear', 'herramientas_no_consumibles', 28, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"torquimetro\",\"cantidad\":\"25\",\"codigo_barras\":\"HERR_637E8466\"}', '2025-05-14 15:26:47', '::1'),
(376, 10, 'crear', 'herramientas_no_consumibles', 29, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"multimetro\",\"cantidad\":\"40\",\"codigo_barras\":\"HERR_64E919D6\"}', '2025-05-14 15:27:10', '::1'),
(377, 10, 'crear', 'herramientas_no_consumibles', 30, '{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"gato hidraulico\",\"cantidad\":\"20\",\"codigo_barras\":\"HERR_65CEF087\"}', '2025-05-14 15:27:24', '::1'),
(378, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-14 16:00:18', '::1'),
(379, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-20 12:29:13', '::1'),
(380, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-20 13:33:32', '::1'),
(381, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-20 15:20:57', '::1'),
(382, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 12:08:52', '::1'),
(383, 10, 'prestamo', 'prestamos', 68, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 90, \"tipo\": \"consumible\"}', '2025-05-21 12:11:12', NULL),
(384, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 10}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-05-21 12:11:12', NULL),
(385, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 10, \"nuevo\": 10}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-05-21 12:11:49', NULL),
(386, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 13:57:18', '::1'),
(387, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-21 14:04:39', '::1'),
(388, 14, 'prestamo', 'prestamos', 69, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 22, \"tipo\": \"no_consumible\"}', '2025-05-21 14:05:28', NULL),
(389, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 14:06:56', '::1'),
(390, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 14:17:16', '::1'),
(391, 10, 'prestamo', 'prestamos', 70, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-05-21 14:18:57', NULL),
(392, 10, 'devolucion', 'prestamos', 70, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-05-21 14:19:21', NULL),
(393, NULL, 'devolucion', 'prestamos', 70, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-05-21 14:19:21', '::1'),
(394, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 15:08:00', '::1'),
(395, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-21 15:41:33', '::1'),
(396, 14, 'devolucion', 'prestamos', 69, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 22, \"tipo\": \"no_consumible\"}', '2025-05-21 15:44:39', NULL),
(397, NULL, 'devolucion', 'prestamos', 69, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":22,\"tipo\":\"no_consumible\"}', '2025-05-21 15:44:39', '::1'),
(398, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-22 13:12:22', '::1'),
(399, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-22 15:44:23', '::1'),
(400, 10, 'crear', 'herramientas_no_consumibles', 31, '{\"descripcion\":\"Nueva herramienta no consumible creada\",\"nombre\":\"pala\",\"cantidad\":\"30\"}', '2025-05-22 16:03:55', '::1'),
(401, 10, 'eliminar', 'herramientas_no_consumibles', 31, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"pala\",\"cantidad\":30}', '2025-05-22 16:48:23', '::1'),
(402, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-22 17:45:27', '::1'),
(403, 10, 'prestamo', 'prestamos', 71, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"lola perez\", \"cantidad\": 15, \"tipo\": \"no_consumible\"}', '2025-05-22 17:50:10', NULL),
(404, 10, 'prestamo', 'prestamos', 72, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tuercas\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 60, \"tipo\": \"consumible\"}', '2025-05-22 17:52:21', NULL),
(405, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 40}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-05-22 17:52:21', NULL),
(406, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 40, \"nuevo\": 40}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-05-22 17:52:50', NULL),
(407, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 13:15:56', '::1'),
(408, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-26 13:31:48', '::1'),
(409, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 13:31:58', '::1'),
(410, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin1\"}', '2025-05-26 14:00:39', '::1'),
(411, 5, 'modificar', 'usuarios', 10, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":{\"usuario_anterior\":\"admin1\",\"usuario_nuevo\":\"admin\",\"cambio_contrase\\u00f1a\":true}}', '2025-05-26 14:07:22', '::1'),
(412, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 14:07:37', '::1'),
(413, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 14:07:39', '::1'),
(414, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-26 14:09:02', '::1'),
(415, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-26 14:09:53', '::1'),
(416, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-26 15:17:40', '::1'),
(417, 14, 'prestamo', 'prestamos', 73, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 33, \"tipo\": \"no_consumible\"}', '2025-05-26 15:18:34', NULL),
(418, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 15:18:53', '::1'),
(419, 14, 'devolucion', 'prestamos', 73, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 33, \"tipo\": \"no_consumible\"}', '2025-05-26 15:23:53', NULL),
(420, NULL, 'devolucion', 'prestamos', 73, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":33,\"tipo\":\"no_consumible\"}', '2025-05-26 15:23:53', '::1'),
(421, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 15:37:46', '::1'),
(422, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 15:37:48', '::1'),
(423, 10, 'devolucion', 'prestamos', 71, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"lola perez\", \"cantidad\": 15, \"tipo\": \"no_consumible\"}', '2025-05-26 15:37:57', NULL),
(424, NULL, 'devolucion', 'prestamos', 71, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"gato hidraulico\",\"aprendiz\":\"lola perez\",\"cantidad\":15,\"tipo\":\"no_consumible\"}', '2025-05-26 15:37:57', '::1'),
(425, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-05-26 15:58:39', NULL),
(426, 10, 'prestamo', 'prestamos', 74, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 80, \"tipo\": \"consumible\"}', '2025-05-26 16:40:04', NULL),
(427, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 101, \"nuevo\": 21}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-05-26 16:40:04', NULL),
(428, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 21, \"nuevo\": 21}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-05-26 16:55:16', NULL),
(430, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-26 23:51:54', '::1'),
(431, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-05-27 12:58:57', '::1'),
(432, 5, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":{\"usuario_anterior\":\"aprendiz\",\"usuario_nuevo\":\"aprendiz1\"}}', '2025-05-27 13:17:26', '::1'),
(433, 5, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":{\"usuario_anterior\":\"aprendiz1\",\"usuario_nuevo\":\"aprendiz\"}}', '2025-05-27 13:17:34', '::1'),
(434, 5, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":[]}', '2025-05-27 13:23:32', '::1'),
(435, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 13:29:19', '::1'),
(436, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 13:29:21', '::1'),
(437, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 13:40:54', '::1'),
(438, 5, 'modificar', 'usuarios', 10, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":[]}', '2025-05-27 13:41:14', '::1'),
(439, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 14:14:36', '::1'),
(440, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 14:15:59', '::1'),
(441, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 14:16:07', '::1'),
(442, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 14:16:09', '::1'),
(443, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:16:04', '::1'),
(444, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:16:06', '::1'),
(445, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:17:04', '::1'),
(446, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:17:07', '::1'),
(447, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:45:12', '::1'),
(448, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:45:19', '::1'),
(449, 5, 'modificar', 'usuarios', 10, '{\"descripcion\":\"Actualizaci\\u00f3n de perfil\",\"detalles\":{\"cambio_contrase\\u00f1a\":true}}', '2025-05-27 15:45:59', '::1'),
(450, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-05-27 15:46:08', '::1'),
(451, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-01 00:39:11', '::1'),
(452, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-01 00:43:34', '::1'),
(453, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-03 13:40:15', '::1'),
(454, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-03 14:12:49', '::1'),
(455, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-03 15:19:50', '::1'),
(456, 14, 'prestamo', 'prestamos', 75, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-03 15:21:17', NULL),
(457, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-03 15:21:24', '::1'),
(458, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-04 13:41:09', '::1'),
(459, 14, 'prestamo', 'prestamos', 76, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 13:42:20', NULL),
(460, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-04 13:42:36', '::1'),
(461, 14, 'devolucion', 'prestamos', 76, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 13:44:40', NULL),
(462, NULL, 'devolucion', 'prestamos', 76, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-04 13:44:40', '::1'),
(463, 14, 'devolucion', 'prestamos', 75, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-04 13:45:41', NULL),
(464, NULL, 'devolucion', 'prestamos', 75, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"gato hidraulico\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-04 13:45:41', '::1'),
(465, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-04 13:58:38', '::1'),
(466, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-04 14:10:26', '::1'),
(467, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-04 14:11:00', '::1'),
(468, 10, 'prestamo', 'prestamos', 77, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 14:11:43', NULL),
(469, 10, 'prestamo', 'prestamos', 78, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-04 14:12:24', NULL),
(470, 10, 'devolucion', 'prestamos', 77, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 14:18:12', NULL),
(471, NULL, 'devolucion', 'prestamos', 77, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-04 14:18:12', '::1'),
(472, 10, 'devolucion', 'prestamos', 78, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-04 14:18:28', NULL),
(473, NULL, 'devolucion', 'prestamos', 78, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-06-04 14:18:28', '::1'),
(474, 10, 'prestamo', 'prestamos', 79, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 14:19:47', NULL),
(475, 10, 'devolucion', 'prestamos', 79, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 14:19:56', NULL),
(476, NULL, 'devolucion', 'prestamos', 79, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-04 14:19:56', '::1'),
(477, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-04 14:59:05', '::1'),
(478, 10, 'prestamo', 'prestamos', 80, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-04 14:59:57', NULL),
(479, 10, 'prestamo', 'prestamos', 81, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 22, \"tipo\": \"no_consumible\"}', '2025-06-04 16:46:05', NULL),
(480, 10, 'devolucion', 'prestamos', 81, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 22, \"tipo\": \"no_consumible\"}', '2025-06-04 16:46:50', NULL),
(481, NULL, 'devolucion', 'prestamos', 81, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":22,\"tipo\":\"no_consumible\"}', '2025-06-04 16:46:50', '::1'),
(482, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-05 12:13:36', '::1'),
(483, 10, 'crear', 'herramientas_consumibles', 13, '{\"descripcion\":\"Nueva herramienta consumible creada\",\"nombre\":\"PUNTILLA\",\"cantidad\":\"30\",\"estado\":\"lleno\"}', '2025-06-05 12:30:54', '::1'),
(484, 10, 'modificar', 'herramientas_consumibles', 13, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"PUNTILLA\", \"cambios\": {\"cantidad\": {\"anterior\": 30, \"nuevo\": 30}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-06-05 12:30:55', NULL),
(485, 10, 'crear', 'herramientas_no_consumibles', 32, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"taladro\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:24:40', NULL),
(486, 10, 'crear', 'herramientas_no_consumibles', 33, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:24:40', NULL),
(487, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-05 13:25:49', '::1'),
(488, 10, 'eliminar', 'herramientas_no_consumibles', 32, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"taladro\",\"cantidad\":0}', '2025-06-05 13:27:33', '::1'),
(489, 10, 'eliminar', 'herramientas_no_consumibles', 33, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":0}', '2025-06-05 13:27:39', '::1'),
(490, 10, 'crear', 'herramientas_no_consumibles', 34, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"taladro\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:33:42', NULL),
(491, 10, 'crear', 'herramientas_no_consumibles', 35, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:33:42', NULL),
(492, 10, 'eliminar', 'herramientas_no_consumibles', 34, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"taladro\",\"cantidad\":0}', '2025-06-05 13:34:36', '::1'),
(493, 10, 'eliminar', 'herramientas_no_consumibles', 35, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":0}', '2025-06-05 13:34:41', '::1'),
(494, 10, 'crear', 'herramientas_no_consumibles', 36, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"taladro\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:34:51', NULL),
(495, 10, 'crear', 'herramientas_no_consumibles', 37, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 0, \"estado\": null}', '2025-06-05 13:34:51', NULL),
(496, 10, 'eliminar', 'herramientas_no_consumibles', 36, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"taladro\",\"cantidad\":0}', '2025-06-05 13:38:55', '::1'),
(497, 10, 'eliminar', 'herramientas_no_consumibles', 37, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":0}', '2025-06-05 13:39:01', '::1'),
(498, 10, 'crear', 'herramientas_no_consumibles', 38, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"taladro\", \"cantidad\": 0, \"estado\": \"Activa\"}', '2025-06-05 13:39:39', NULL),
(499, 10, 'crear', 'herramientas_no_consumibles', 39, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 0, \"estado\": \"Activa\"}', '2025-06-05 13:39:39', NULL),
(500, 10, 'eliminar', 'herramientas_no_consumibles', 38, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"taladro\",\"cantidad\":0}', '2025-06-05 13:39:46', '::1'),
(501, 10, 'eliminar', 'herramientas_no_consumibles', 39, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":0}', '2025-06-05 13:39:50', '::1'),
(504, 10, 'crear', 'herramientas_no_consumibles', 44, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 13:45:10', NULL),
(505, 10, 'crear', 'herramientas_no_consumibles', 45, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 50, \"estado\": \"Activa\"}', '2025-06-05 13:45:10', NULL),
(506, 10, 'eliminar', 'herramientas_no_consumibles', 44, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":100}', '2025-06-05 13:47:09', '::1'),
(507, 10, 'eliminar', 'herramientas_no_consumibles', 45, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":50}', '2025-06-05 13:47:15', '::1'),
(508, 10, 'crear', 'herramientas_no_consumibles', 46, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 13:49:59', NULL),
(509, 10, 'crear', 'herramientas_no_consumibles', 47, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 50, \"estado\": \"Activa\"}', '2025-06-05 13:49:59', NULL),
(510, 10, 'crear', 'herramientas_consumibles', 14, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"grasa\", \"cantidad\": 100, \"estado\": \"lleno\"}', '2025-06-05 13:50:23', NULL),
(511, 10, 'crear', 'herramientas_consumibles', 15, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"palin\", \"cantidad\": 50, \"estado\": \"\"}', '2025-06-05 13:50:23', NULL),
(512, 10, 'modificar', 'herramientas_consumibles', 15, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"palin\", \"cambios\": {\"cantidad\": {\"anterior\": 50, \"nuevo\": 50}, \"estado\": {\"anterior\": \"\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-05 13:50:24', NULL),
(513, 10, 'eliminar', 'herramientas_consumibles', 14, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":100}', '2025-06-05 13:50:32', '::1'),
(514, 10, 'eliminar', 'herramientas_consumibles', 15, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":50}', '2025-06-05 13:50:37', '::1'),
(515, 10, 'eliminar', 'herramientas_no_consumibles', 46, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"grasa\",\"cantidad\":100}', '2025-06-05 13:50:49', '::1'),
(516, 10, 'eliminar', 'herramientas_no_consumibles', 47, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":50}', '2025-06-05 13:50:56', '::1'),
(517, 10, 'crear', 'herramientas_no_consumibles', 48, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 13:54:43', NULL),
(518, 10, 'crear', 'herramientas_consumibles', 16, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"\"}', '2025-06-05 13:54:57', NULL),
(519, 10, 'modificar', 'herramientas_consumibles', 16, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"palin\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-05 13:54:58', NULL),
(520, 10, 'eliminar', 'herramientas_consumibles', 16, '{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 13:55:05', '::1'),
(521, 10, 'crear', 'herramientas_no_consumibles', 49, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:11:22', NULL),
(522, 10, 'eliminar', 'herramientas_no_consumibles', 49, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:11:33', '::1'),
(523, 10, 'eliminar', 'herramientas_no_consumibles', 48, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:11:40', '::1'),
(524, 10, 'crear', 'herramientas_no_consumibles', 50, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:18:17', NULL),
(525, 10, 'crear', 'herramientas_no_consumibles', 51, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:18:33', NULL),
(526, 10, 'eliminar', 'herramientas_no_consumibles', 50, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:21:34', '::1'),
(527, 10, 'eliminar', 'herramientas_no_consumibles', 51, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:21:39', '::1'),
(528, 10, 'crear', 'herramientas_no_consumibles', 52, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:51:55', NULL),
(529, 10, 'crear', 'herramientas_no_consumibles', 53, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:52:15', NULL),
(530, 10, 'eliminar', 'herramientas_no_consumibles', 52, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:52:24', '::1'),
(531, 10, 'eliminar', 'herramientas_no_consumibles', 53, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:52:30', '::1'),
(532, 10, 'crear', 'herramientas_no_consumibles', 54, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:52:45', NULL),
(533, 10, 'eliminar', 'herramientas_no_consumibles', 54, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:52:56', '::1'),
(534, 10, 'crear', 'herramientas_no_consumibles', 55, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:54:36', NULL),
(535, 10, 'eliminar', 'herramientas_no_consumibles', 55, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:59:13', '::1'),
(536, 10, 'crear', 'herramientas_no_consumibles', 56, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 14:59:23', NULL),
(537, 10, 'eliminar', 'herramientas_no_consumibles', 56, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 14:59:37', '::1'),
(538, 10, 'crear', 'herramientas_no_consumibles', 57, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:02:31', NULL),
(539, 10, 'eliminar', 'herramientas_no_consumibles', 57, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:09:22', '::1'),
(540, 10, 'crear', 'herramientas_no_consumibles', 58, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:09:31', NULL),
(541, 10, 'crear', 'herramientas_no_consumibles', 59, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:09:34', NULL),
(542, 10, 'crear', 'herramientas_no_consumibles', 60, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:09:36', NULL),
(543, 10, 'crear', 'herramientas_no_consumibles', 61, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:09:37', NULL),
(544, 10, 'crear', 'herramientas_no_consumibles', 62, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:10:25', NULL),
(545, 10, 'crear', 'herramientas_no_consumibles', 63, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:10:28', NULL),
(546, 10, 'eliminar', 'herramientas_no_consumibles', 59, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:11:44', '::1'),
(547, NULL, 'eliminar', 'herramientas_no_consumibles', 58, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:12:07', NULL),
(548, NULL, 'eliminar', 'herramientas_no_consumibles', 60, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:12:07', NULL),
(549, NULL, 'eliminar', 'herramientas_no_consumibles', 61, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:12:07', NULL),
(550, NULL, 'eliminar', 'herramientas_no_consumibles', 62, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:12:07', NULL),
(551, NULL, 'eliminar', 'herramientas_no_consumibles', 63, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:12:07', NULL),
(552, 10, 'crear', 'herramientas_no_consumibles', 64, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:12:20', NULL),
(553, 10, 'crear', 'herramientas_no_consumibles', 65, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:40', NULL),
(554, 10, 'crear', 'herramientas_no_consumibles', 66, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:41', NULL),
(555, 10, 'crear', 'herramientas_no_consumibles', 67, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:42', NULL),
(556, 10, 'crear', 'herramientas_no_consumibles', 68, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:42', NULL),
(557, 10, 'crear', 'herramientas_no_consumibles', 69, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:43', NULL),
(558, 10, 'crear', 'herramientas_no_consumibles', 70, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:43', NULL),
(559, 10, 'crear', 'herramientas_no_consumibles', 71, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:43', NULL),
(560, 10, 'crear', 'herramientas_no_consumibles', 72, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:43', NULL),
(561, 10, 'crear', 'herramientas_no_consumibles', 73, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:44', NULL),
(562, 10, 'crear', 'herramientas_no_consumibles', 74, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:44', NULL),
(563, 10, 'crear', 'herramientas_no_consumibles', 75, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:44', NULL),
(564, 10, 'crear', 'herramientas_no_consumibles', 76, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:44', NULL),
(565, 10, 'crear', 'herramientas_no_consumibles', 77, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:45', NULL),
(566, 10, 'crear', 'herramientas_no_consumibles', 78, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:45', NULL),
(567, 10, 'crear', 'herramientas_no_consumibles', 79, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:18:45', NULL),
(568, 10, 'eliminar', 'herramientas_no_consumibles', 79, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:20:08', '::1'),
(569, NULL, 'eliminar', 'herramientas_no_consumibles', 64, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(570, NULL, 'eliminar', 'herramientas_no_consumibles', 65, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(571, NULL, 'eliminar', 'herramientas_no_consumibles', 66, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(572, NULL, 'eliminar', 'herramientas_no_consumibles', 67, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(573, NULL, 'eliminar', 'herramientas_no_consumibles', 68, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(574, NULL, 'eliminar', 'herramientas_no_consumibles', 69, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(575, NULL, 'eliminar', 'herramientas_no_consumibles', 70, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(576, NULL, 'eliminar', 'herramientas_no_consumibles', 71, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(577, NULL, 'eliminar', 'herramientas_no_consumibles', 72, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(578, NULL, 'eliminar', 'herramientas_no_consumibles', 73, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(579, NULL, 'eliminar', 'herramientas_no_consumibles', 74, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(580, NULL, 'eliminar', 'herramientas_no_consumibles', 75, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(581, NULL, 'eliminar', 'herramientas_no_consumibles', 76, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(582, NULL, 'eliminar', 'herramientas_no_consumibles', 77, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(583, NULL, 'eliminar', 'herramientas_no_consumibles', 78, '{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"palin\", \"cantidad\": 100}', '2025-06-05 15:20:41', NULL),
(584, 10, 'crear', 'herramientas_no_consumibles', 80, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:24:31', NULL),
(585, 10, 'crear', 'herramientas_no_consumibles', 81, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:28:47', NULL),
(586, 10, 'eliminar', 'herramientas_no_consumibles', 81, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:30:38', '::1'),
(587, 10, 'crear', 'herramientas_no_consumibles', 82, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:30:48', NULL),
(588, 10, 'eliminar', 'herramientas_no_consumibles', 80, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:30:56', '::1'),
(589, 10, 'eliminar', 'herramientas_no_consumibles', 82, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:31:01', '::1'),
(590, 10, 'crear', 'herramientas_no_consumibles', 83, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:31:14', NULL),
(591, 10, 'crear', 'herramientas_consumibles', 17, '{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"GRASA\", \"cantidad\": 100, \"estado\": \"medio\"}', '2025-06-05 15:32:36', NULL),
(592, 10, 'modificar', 'herramientas_consumibles', 17, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"GRASA\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-05 15:32:37', NULL);
INSERT INTO `auditorias` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `fecha_accion`, `ip_usuario`) VALUES
(593, 10, 'crear', 'herramientas_no_consumibles', 84, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:33:47', NULL),
(594, 10, 'eliminar', 'herramientas_no_consumibles', 84, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:33:55', '::1'),
(595, 10, 'eliminar', 'herramientas_no_consumibles', 83, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:34:00', '::1'),
(596, 10, 'crear', 'herramientas_no_consumibles', 85, '{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"palin\", \"cantidad\": 100, \"estado\": \"Activa\"}', '2025-06-05 15:40:56', NULL),
(597, 10, 'eliminar', 'herramientas_no_consumibles', 85, '{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"palin\",\"cantidad\":100}', '2025-06-05 15:41:01', '::1'),
(598, 10, 'devolucion', 'prestamos', 80, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-05 16:04:10', NULL),
(599, NULL, 'devolucion', 'prestamos', 80, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-05 16:04:10', '::1'),
(600, 10, 'prestamo', 'prestamos', 82, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"multimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 20, \"tipo\": \"no_consumible\"}', '2025-06-05 16:06:03', NULL),
(601, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-05 16:57:43', '::1'),
(602, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-05 18:10:58', '::1'),
(603, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 200, \"nuevo\": 200}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-05 18:15:08', NULL),
(604, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-05 18:15:40', '::1'),
(605, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-09 12:06:21', '::1'),
(606, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-09 13:39:45', '::1'),
(607, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-09 14:11:21', '::1'),
(608, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-09 15:38:18', '::1'),
(609, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-09 16:21:48', '::1'),
(610, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-09 16:23:12', '::1'),
(611, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-09 16:23:20', '::1'),
(612, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-09 16:25:02', '::1'),
(613, NULL, 'eliminar', 'usuarios', 16, '{\"descripcion\": \"Eliminación de usuario\", \"usuario\": \"mateoa\", \"rol\": \"aprendiz\"}', '2025-06-09 16:32:32', NULL),
(614, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-09 16:46:29', '::1'),
(615, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-09 16:46:44', '::1'),
(616, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-09 16:46:56', '::1'),
(617, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-09 16:47:04', '::1'),
(618, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 12:56:59', '::1'),
(619, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 12:58:13', '::1'),
(620, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:04:25', '::1'),
(621, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 13:04:41', '::1'),
(622, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-11 13:04:49', '::1'),
(623, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:09:36', '::1'),
(624, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:09:52', '::1'),
(625, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:12:43', '::1'),
(626, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:15:05', '::1'),
(627, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-11 13:15:18', '::1'),
(628, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 13:15:36', '::1'),
(629, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 13:16:23', '::1'),
(630, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 13:17:24', '::1'),
(631, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-11 13:19:42', '::1'),
(632, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 13:22:11', '::1'),
(633, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 13:22:19', '::1'),
(634, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-11 13:22:40', '::1'),
(635, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 13:23:00', '::1'),
(636, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-11 14:49:31', '::1'),
(637, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-11 14:52:30', '::1'),
(638, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 15:15:12', '::1'),
(639, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 70}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-11 15:47:51', NULL),
(640, NULL, 'prestamo', 'prestamos', 83, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 30, \"tipo\": \"consumible\"}', '2025-06-11 15:47:51', NULL),
(641, NULL, 'prestamo', 'prestamos', 84, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"llave 8\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-11 15:48:45', NULL),
(642, NULL, 'devolucion', 'prestamos', 84, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"llave 8\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-11 15:49:27', NULL),
(643, NULL, 'devolucion', 'prestamos', 84, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"llave 8\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-11 15:49:27', '::1'),
(644, NULL, '', 'reservas_herramientas', 8, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-11 16:02:28', NULL),
(645, NULL, '', 'reservas_herramientas', 11, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-11 16:02:32', NULL),
(646, 10, 'modificar', 'usuarios', 17, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"invi\",\"rol_anterior\":\"aprendiz\",\"rol_nuevo\":\"administrador\"}', '2025-06-11 16:06:04', '::1'),
(647, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 16:06:21', '::1'),
(648, 10, 'modificar', 'usuarios', 17, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"invi\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}', '2025-06-11 16:06:34', '::1'),
(649, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 16:06:57', '::1'),
(650, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-11 16:21:36', '::1'),
(651, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-11 16:40:01', '::1'),
(652, 10, 'modificar', 'usuarios', 17, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"invi\",\"rol_anterior\":\"aprendiz\",\"rol_nuevo\":\"administrador\"}', '2025-06-11 16:42:14', '::1'),
(653, 10, 'modificar', 'usuarios', 17, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"invi\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"aprendiz\"}', '2025-06-11 16:42:22', '::1'),
(654, NULL, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-12 12:54:47', '::1'),
(655, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-12 12:55:24', '::1'),
(656, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 200, \"nuevo\": 150}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-12 12:55:35', NULL),
(657, NULL, 'prestamo', 'prestamos', 85, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 50, \"tipo\": \"consumible\"}', '2025-06-12 12:55:35', NULL),
(658, 10, 'prestamo', 'prestamos', 86, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tuercas\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 35, \"tipo\": \"consumible\"}', '2025-06-12 12:57:03', NULL),
(659, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 40, \"nuevo\": 5}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-06-12 12:57:03', NULL),
(660, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-12 13:07:31', '::1'),
(661, 10, 'eliminar', 'usuarios', 17, '{\"descripcion\":\"Desactivaci\\u00f3n de usuario\",\"usuario\":\"invi\",\"rol\":\"aprendiz\"}', '2025-06-12 13:13:16', '::1'),
(662, NULL, '', 'usuarios', 17, '{\"descripcion\": \"Desactivación de usuario\", \"usuario\": \"invi\", \"rol\": \"aprendiz\"}', '2025-06-12 13:13:16', NULL),
(663, 17, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-12 13:13:43', '::1'),
(664, 17, 'crear', 'usuarios', 17, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"invi\"}', '2025-06-12 13:20:07', '::1'),
(665, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-12 13:22:21', '::1'),
(666, 15, 'prestamo', 'prestamos', 87, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-12 13:23:51', NULL),
(667, 15, 'devolucion', 'prestamos', 87, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-12 13:27:52', NULL),
(668, NULL, 'devolucion', 'prestamos', 87, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"torquimetro\",\"aprendiz\":\"lina gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-12 13:27:52', '::1'),
(669, 15, 'prestamo', 'prestamos', 88, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-12 13:28:14', NULL),
(670, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-12 14:08:47', '::1'),
(671, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 150, \"nuevo\": 130}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-12 14:09:52', NULL),
(672, NULL, 'prestamo', 'prestamos', 89, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 20, \"tipo\": \"consumible\"}', '2025-06-12 14:09:52', NULL),
(673, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-12 15:03:25', '::1'),
(674, NULL, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 5, \"nuevo\": -35}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-06-12 15:22:13', NULL),
(675, NULL, 'prestamo', 'prestamos', 90, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tuercas\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 40, \"tipo\": \"consumible\"}', '2025-06-12 15:22:13', NULL),
(676, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": -35, \"nuevo\": -35}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-06-12 15:23:30', NULL),
(677, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-12 15:23:39', NULL),
(678, 10, 'prestamo', 'prestamos', 91, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tuercas\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 50, \"tipo\": \"consumible\"}', '2025-06-12 15:24:38', NULL),
(679, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 50}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-12 15:24:38', NULL),
(680, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 130, \"nuevo\": 107}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-12 16:02:39', NULL),
(681, NULL, 'prestamo', 'prestamos', 92, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 23, \"tipo\": \"consumible\"}', '2025-06-12 16:02:39', NULL),
(682, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-12 16:45:03', '::1'),
(683, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-12 16:45:05', '::1'),
(684, 18, 'crear', 'usuarios', 18, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"luis\"}', '2025-06-12 16:49:55', '::1'),
(685, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-16 12:04:59', '::1'),
(686, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 50, \"nuevo\": 50}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:08:25', NULL),
(687, 10, '', 'reservas_herramientas', 17, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-16 12:33:21', NULL),
(688, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-16 12:34:44', '::1'),
(689, 10, 'prestamo', 'prestamos', 93, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 100, \"tipo\": \"consumible\"}', '2025-06-16 12:37:30', NULL),
(690, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 107, \"nuevo\": 7}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:37:30', NULL),
(691, NULL, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 50, \"nuevo\": 0}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:38:09', NULL),
(692, 10, 'prestamo', 'prestamos', 94, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tuercas\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 50, \"tipo\": \"consumible\"}', '2025-06-16 12:38:09', NULL),
(693, 10, '', 'reservas_herramientas', 20, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-16 12:38:36', NULL),
(694, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 7, \"nuevo\": 7}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:44:54', NULL),
(695, 10, 'modificar', 'herramientas_consumibles', 12, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tuercas\", \"cambios\": {\"cantidad\": {\"anterior\": 0, \"nuevo\": 0}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:44:54', NULL),
(696, 10, 'prestamo', 'prestamos', 95, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 5, \"tipo\": \"consumible\"}', '2025-06-16 12:55:36', NULL),
(697, 10, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 7, \"nuevo\": 2}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-16 12:55:36', NULL),
(698, 10, '', 'reservas_herramientas', 21, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-16 12:55:59', NULL),
(699, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-16 13:36:23', '::1'),
(700, 10, 'prestamo', 'prestamos', 96, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 6, \"tipo\": \"no_consumible\"}', '2025-06-16 13:37:09', NULL),
(701, 10, '', 'reservas_herramientas', 22, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-16 13:37:09', NULL),
(702, 10, 'devolucion', 'prestamos', 96, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 6, \"tipo\": \"no_consumible\"}', '2025-06-16 13:49:56', NULL),
(703, NULL, 'devolucion', 'prestamos', 96, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":6,\"tipo\":\"no_consumible\"}', '2025-06-16 13:49:56', '::1'),
(704, 10, 'prestamo', 'prestamos', 97, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"llave 8\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-16 14:04:48', NULL),
(705, 10, '', 'reservas_herramientas', 23, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-16 14:04:48', NULL),
(706, 10, 'prestamo', 'prestamos', 98, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"alan brito\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-16 14:05:27', NULL),
(707, 10, '', 'reservas_herramientas', 24, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-16 14:05:27', NULL),
(708, 10, 'prestamo', 'prestamos', 99, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-16 14:07:38', NULL),
(709, 10, '', 'reservas_herramientas', 25, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-16 14:07:38', NULL),
(710, 21, 'crear', 'usuarios', 21, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"natalia cardona\"}', '2025-06-16 14:08:37', '::1'),
(711, 10, 'prestamo', 'prestamos', 100, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"llave 8\", \"aprendiz\": \"natalia cardona\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-16 14:09:33', NULL),
(712, 10, '', 'reservas_herramientas', 26, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-16 14:09:33', NULL),
(713, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-16 14:13:07', '::1'),
(714, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-16 15:03:02', '::1'),
(715, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-16 15:16:37', '::1'),
(716, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-16 15:16:53', '::1'),
(717, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-16 15:24:28', '::1'),
(718, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-16 15:24:45', '::1'),
(719, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-16 16:03:22', '::1'),
(720, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 13:08:52', '::1'),
(721, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-17 13:09:12', '::1'),
(722, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 13:55:29', '::1'),
(723, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 14:12:15', '::1'),
(724, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 14:12:59', '::1'),
(725, 15, 'prestamo', 'prestamos', 101, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-17 14:13:56', NULL),
(726, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-17 14:14:09', '::1'),
(727, 10, 'prestamo', 'prestamos', 102, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 69, \"tipo\": \"consumible\"}', '2025-06-17 14:17:34', NULL),
(728, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 70, \"nuevo\": 1}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-17 14:17:34', NULL),
(729, NULL, 'modificar', 'herramientas_consumibles', 1, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad\": {\"anterior\": 2, \"nuevo\": 0}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-17 14:17:56', NULL),
(730, 10, 'prestamo', 'prestamos', 103, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"consumible\"}', '2025-06-17 14:17:56', NULL),
(731, 10, '', 'reservas_herramientas', 27, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-17 14:17:56', NULL),
(732, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 14:20:27', '::1'),
(733, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 14:21:19', '::1'),
(734, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 14:57:09', '::1'),
(735, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-17 14:57:45', '::1'),
(736, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 15:19:32', '::1'),
(737, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 15:27:20', '::1'),
(738, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-17 15:28:47', '::1'),
(739, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 15:30:56', '::1'),
(740, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 16:15:04', '::1'),
(741, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-17 16:15:22', '::1'),
(742, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-17 16:16:58', '::1'),
(743, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-17 16:17:23', '::1'),
(744, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 1, \"nuevo\": 1}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-17 16:20:46', NULL),
(745, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-18 12:44:54', '::1'),
(746, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-18 13:17:52', '::1'),
(747, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-18 13:19:03', '::1'),
(748, 15, 'prestamo', 'prestamos', 104, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-18 13:19:55', NULL),
(749, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-18 13:20:10', '::1'),
(750, 10, 'prestamo', 'prestamos', 105, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-18 13:23:32', NULL),
(751, 10, '', 'reservas_herramientas', 28, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-18 13:23:32', NULL),
(752, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-18 13:25:52', '::1'),
(753, 10, 'prestamo', 'prestamos', 106, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 9, \"tipo\": \"consumible\"}', '2025-06-18 13:31:13', NULL),
(754, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 10, \"nuevo\": 1}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-06-18 13:31:13', NULL),
(755, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-18 15:15:24', '::1'),
(756, 22, 'crear', 'usuarios', 22, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"jhon\"}', '2025-06-24 13:01:38', '::1'),
(757, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:01:56', '::1'),
(758, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:41:57', '::1'),
(759, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:41:59', '::1'),
(760, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:42:04', '::1'),
(761, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:42:06', '::1'),
(762, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 13:42:08', '::1'),
(763, 10, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"aprendiz\",\"rol_nuevo\":\"almacenista\"}', '2025-06-24 13:42:32', '::1'),
(764, 10, 'modificar', 'usuarios', 15, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"mateo\",\"rol_anterior\":\"almacenista\",\"rol_nuevo\":\"administrador\"}', '2025-06-24 13:43:42', '::1'),
(765, 10, 'modificar', 'usuarios', 15, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"mateo\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"aprendiz\"}', '2025-06-24 13:43:46', '::1'),
(766, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-24 13:44:10', '::1'),
(767, 10, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"almacenista\",\"rol_nuevo\":\"administrador\"}', '2025-06-24 13:44:19', '::1'),
(768, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-24 13:44:27', '::1'),
(769, 10, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"aprendiz\"}', '2025-06-24 13:44:34', '::1'),
(770, 10, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"aprendiz\",\"rol_nuevo\":\"almacenista\"}', '2025-06-24 13:53:03', '::1'),
(771, 10, 'modificar', 'usuarios', 14, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"almacenista\",\"rol_nuevo\":\"aprendiz\"}', '2025-06-24 13:53:08', '::1'),
(772, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-24 15:11:46', '::1'),
(773, 10, 'prestamo', 'prestamos', 107, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"natalia cardona\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:10', NULL),
(774, 10, 'devolucion', 'prestamos', 107, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"natalia cardona\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:18', NULL),
(775, NULL, 'devolucion', 'prestamos', 107, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"natalia cardona\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:18', '::1'),
(776, 10, 'devolucion', 'prestamos', 105, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:21', NULL),
(777, NULL, 'devolucion', 'prestamos', 105, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda \",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:21', '::1'),
(778, 15, 'devolucion', 'prestamos', 104, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:24', NULL),
(779, NULL, 'devolucion', 'prestamos', 104, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"jeronimo sanchez holgin\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:24', '::1'),
(780, 10, 'devolucion', 'prestamos', 97, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"llave 8\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:34', NULL),
(781, NULL, 'devolucion', 'prestamos', 97, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"llave 8\",\"aprendiz\":\"jeronimo sanchez holgin\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:34', '::1'),
(782, 15, 'devolucion', 'prestamos', 101, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:37', NULL),
(783, NULL, 'devolucion', 'prestamos', 101, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"gato hidraulico\",\"aprendiz\":\"SANTIAGO GIRTALOD\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:37', '::1'),
(784, 10, 'devolucion', 'prestamos', 100, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"llave 8\", \"aprendiz\": \"natalia cardona\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:39', NULL),
(785, NULL, 'devolucion', 'prestamos', 100, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"llave 8\",\"aprendiz\":\"natalia cardona\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:39', '::1'),
(786, 10, 'devolucion', 'prestamos', 99, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"gato hidraulico\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-06-24 15:15:42', NULL),
(787, NULL, 'devolucion', 'prestamos', 99, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"gato hidraulico\",\"aprendiz\":\"SANTIAGO GIRTALOD\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-06-24 15:15:42', '::1'),
(788, 10, 'modificar', 'usuarios', 15, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"mateo\",\"rol_anterior\":\"aprendiz\",\"rol_nuevo\":\"almacenista\"}', '2025-06-24 15:22:29', '::1'),
(789, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-24 15:27:39', '::1'),
(790, 10, 'prestamo', 'prestamos', 108, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"julio jaramilo\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:38:39', NULL),
(791, 10, '', 'reservas_herramientas', 29, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-24 15:38:39', NULL),
(792, 10, '', 'reservas_herramientas', 30, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-24 15:54:04', NULL),
(793, 10, 'devolucion', 'prestamos', 108, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"julio jaramilo\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-24 15:54:18', NULL),
(794, NULL, 'devolucion', 'prestamos', 108, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"julio jaramilo\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-24 15:54:18', '::1'),
(795, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-24 16:00:30', '::1'),
(796, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-24 16:49:21', '::1'),
(797, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-24 16:49:28', '::1'),
(798, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 12:24:55', '::1'),
(799, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-25 12:25:39', '::1'),
(800, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-25 12:27:41', '::1'),
(801, 10, 'prestamo', 'prestamos', 109, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 12:57:18', NULL),
(802, 10, 'prestamo', 'prestamos', 110, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 12:57:55', NULL),
(803, 10, 'prestamo', 'prestamos', 111, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 12:58:08', NULL),
(804, 10, 'prestamo', 'prestamos', 112, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 12:59:08', NULL),
(805, 10, 'prestamo', 'prestamos', 113, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 12:59:24', NULL),
(806, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-25 13:01:39', '::1'),
(807, 15, 'prestamo', 'prestamos', 114, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:01:59', NULL),
(808, 10, 'prestamo', 'prestamos', 115, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:09:58', NULL),
(809, 10, 'prestamo', 'prestamos', 116, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:10:09', NULL),
(810, 15, 'prestamo', 'prestamos', 117, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"carlos1\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:10:39', NULL),
(811, 15, 'prestamo', 'prestamos', 118, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"llave 8\", \"aprendiz\": \"carlos1\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:12:10', NULL),
(812, 10, 'devolucion', 'prestamos', 113, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:12:26', NULL),
(813, NULL, 'devolucion', 'prestamos', 113, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"lina gonzales\",\"cantidad\":1,\"tipo\":\"no_consumible\"}', '2025-06-25 13:12:26', '::1'),
(814, 15, 'prestamo', 'prestamos', 119, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"llave 8\", \"aprendiz\": \"carlos10\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:12:55', NULL),
(815, 15, 'devolucion', 'prestamos', 117, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"carlos1\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:04', NULL),
(816, NULL, 'devolucion', 'prestamos', 117, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"carlos1\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:04', '::1'),
(817, 15, 'devolucion', 'prestamos', 118, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"llave 8\", \"aprendiz\": \"carlos1\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:09', NULL),
(818, NULL, 'devolucion', 'prestamos', 118, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"llave 8\",\"aprendiz\":\"carlos1\",\"cantidad\":1,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:09', '::1'),
(819, 10, 'devolucion', 'prestamos', 116, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:15', NULL),
(820, NULL, 'devolucion', 'prestamos', 116, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":1,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:15', '::1'),
(821, 15, 'devolucion', 'prestamos', 114, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:22', NULL),
(822, NULL, 'devolucion', 'prestamos', 114, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda \",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:22', '::1'),
(823, 10, 'devolucion', 'prestamos', 110, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:28', NULL),
(824, NULL, 'devolucion', 'prestamos', 110, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:28', '::1'),
(825, 10, 'devolucion', 'prestamos', 82, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"multimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 20, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:33', NULL),
(826, NULL, 'devolucion', 'prestamos', 82, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"multimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":20,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:33', '::1'),
(827, 10, 'devolucion', 'prestamos', 109, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:37', NULL),
(828, NULL, 'devolucion', 'prestamos', 109, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:37', '::1'),
(829, 10, 'devolucion', 'prestamos', 115, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:13:46', NULL),
(830, NULL, 'devolucion', 'prestamos', 115, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda \",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:13:46', '::1'),
(831, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-25 13:14:17', '::1'),
(832, 10, 'prestamo', 'prestamos', 120, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:26:49', NULL),
(833, 10, '', 'reservas_herramientas', 31, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-06-25 13:26:49', NULL),
(834, 10, '', 'reservas_herramientas', 32, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-06-25 13:27:30', NULL),
(835, 15, 'devolucion', 'prestamos', 119, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"llave 8\", \"aprendiz\": \"carlos10\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-06-25 13:27:39', NULL),
(836, NULL, 'devolucion', 'prestamos', 119, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"llave 8\",\"aprendiz\":\"carlos10\",\"cantidad\":2,\"tipo\":\"no_consumible\"}', '2025-06-25 13:27:39', '::1'),
(837, 10, 'devolucion', 'prestamos', 112, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 1, \"tipo\": \"no_consumible\"}', '2025-06-25 13:27:44', NULL),
(838, NULL, 'devolucion', 'prestamos', 112, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda \",\"cantidad\":1,\"tipo\":\"no_consumible\"}', '2025-06-25 13:27:44', '::1'),
(839, 15, 'devolucion', 'prestamos', 88, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"lina gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-06-25 13:27:47', NULL),
(840, NULL, 'devolucion', 'prestamos', 88, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"torquimetro\",\"aprendiz\":\"lina gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}', '2025-06-25 13:27:47', '::1');
INSERT INTO `auditorias` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `fecha_accion`, `ip_usuario`) VALUES
(841, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 14:04:29', '::1'),
(842, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 14:07:45', '::1'),
(843, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 14:12:36', '::1'),
(844, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 14:17:07', '::1'),
(845, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 14:22:30', '::1'),
(846, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-06-25 14:23:25', '::1'),
(847, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-06-25 14:24:01', '::1'),
(848, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-06-25 15:03:58', '::1'),
(849, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-01 12:11:50', '::1'),
(850, 10, 'prestamo', 'prestamos', 121, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"carlos1\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-07-01 12:15:23', NULL),
(851, 10, 'prestamo', 'prestamos', 122, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-07-01 12:56:53', NULL),
(852, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-01 13:12:45', '::1'),
(853, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-01 13:13:02', '::1'),
(854, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-01 13:13:55', '::1'),
(855, 10, 'devolucion', 'prestamos', 122, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"torquimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-07-01 13:28:32', NULL),
(856, NULL, 'devolucion', 'prestamos', 122, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"torquimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-07-01 13:28:32', '::1'),
(857, 10, 'modificar', 'usuarios', 20, '{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"jhon doe\",\"rol_anterior\":\"almacenista\",\"rol_nuevo\":\"aprendiz\"}', '2025-07-01 13:29:06', '::1'),
(858, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-01 15:15:57', '::1'),
(859, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-01 15:16:25', '::1'),
(860, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-01 15:17:12', '::1'),
(861, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-01 15:19:36', '::1'),
(862, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-01 15:32:54', '::1'),
(863, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-01 15:33:41', '::1'),
(864, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 12:58:56', '::1'),
(865, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 12:58:56', '::1'),
(866, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 12:59:32', '::1'),
(867, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 13:01:09', '::1'),
(868, 15, 'prestamo', 'prestamos', 123, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 5, \"tipo\": \"no_consumible\"}', '2025-07-02 13:02:58', NULL),
(869, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 13:03:15', '::1'),
(870, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 13:03:20', '::1'),
(871, NULL, 'modificar', 'herramientas_consumibles', 17, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"GRASA\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 99}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-02 13:08:31', NULL),
(872, 10, 'prestamo', 'prestamos', 124, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"GRASA\", \"aprendiz\": \"alan brito\", \"cantidad\": 1, \"tipo\": \"consumible\"}', '2025-07-02 13:08:31', NULL),
(873, 10, '', 'reservas_herramientas', 33, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-02 13:08:31', NULL),
(874, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 15:23:16', '::1'),
(875, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 15:28:19', '10.2.28.46'),
(876, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 15:28:35', '10.2.17.123'),
(877, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 15:54:27', '10.2.17.123'),
(878, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 15:57:12', '::1'),
(879, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:10:08', '10.2.17.123'),
(880, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 16:10:46', '10.2.17.123'),
(881, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:15:36', '10.2.17.123'),
(882, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:15:46', '::1'),
(883, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:22:31', '::1'),
(884, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:23:55', '10.2.17.123'),
(885, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:24:33', '::1'),
(886, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:25:40', '10.2.17.123'),
(887, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:26:10', '::1'),
(888, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:26:45', '10.2.17.123'),
(889, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:35:21', '10.2.17.123'),
(890, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:36:13', '::1'),
(891, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:36:13', '10.2.17.123'),
(892, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 16:37:07', '10.2.17.123'),
(893, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 16:37:14', '10.2.17.123'),
(894, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:42:24', '10.2.30.231'),
(895, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:43:16', '10.2.30.231'),
(896, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:45:59', '::1'),
(897, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:49:40', '::1'),
(898, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 16:49:51', '10.2.30.231'),
(899, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 16:52:04', '10.2.30.231'),
(900, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 16:52:49', '::1'),
(901, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:02:15', '10.2.26.196'),
(902, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 17:03:26', '10.2.26.196'),
(903, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 17:04:24', '10.2.23.8'),
(904, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-02 17:05:29', '::1'),
(905, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 17:05:39', '::1'),
(906, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-02 17:07:07', '10.2.31.146'),
(907, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:09:45', '10.2.30.231'),
(908, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:10:13', '::1'),
(909, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:10:17', '::1'),
(910, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:12:37', '10.2.31.146'),
(911, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-02 17:18:23', '10.2.30.231'),
(912, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-03 16:51:54', '::1'),
(913, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-05 18:38:45', '192.168.1.3'),
(914, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 12:12:10', '::1'),
(915, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 12:22:36', '10.31.135.32'),
(916, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 12:23:11', '::1'),
(917, 23, 'crear', 'usuarios', 23, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"jromstyle\"}', '2025-07-07 12:41:33', '10.2.20.92'),
(918, 23, 'crear', 'usuarios', 23, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"jromstyle\"}', '2025-07-07 12:51:45', '10.2.20.92'),
(919, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 12:52:13', '10.2.20.92'),
(920, NULL, 'modificar', 'herramientas_consumibles', 17, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"GRASA\", \"cambios\": {\"cantidad\": {\"anterior\": 99, \"nuevo\": 92}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-07 12:54:14', NULL),
(921, 10, 'prestamo', 'prestamos', 125, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"GRASA\", \"aprendiz\": \"jromstyle\", \"cantidad\": 7, \"tipo\": \"consumible\"}', '2025-07-07 12:54:14', NULL),
(922, 10, '', 'reservas_herramientas', 35, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-07 12:54:14', NULL),
(923, 24, 'crear', 'usuarios', 24, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"san\"}', '2025-07-07 12:55:42', '10.2.31.211'),
(924, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 12:57:41', '10.2.31.211'),
(925, NULL, 'modificar', 'herramientas_consumibles', 13, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"PUNTILLA\", \"cambios\": {\"cantidad\": {\"anterior\": 30, \"nuevo\": 23}, \"estado\": {\"anterior\": \"medio\", \"nuevo\": \"medio\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}', '2025-07-07 12:59:14', NULL),
(926, 10, 'prestamo', 'prestamos', 126, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"PUNTILLA\", \"aprendiz\": \"SANTIAGO GIRTALOD\", \"cantidad\": 7, \"tipo\": \"consumible\"}', '2025-07-07 12:59:14', NULL),
(927, 10, '', 'reservas_herramientas', 36, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-07 12:59:14', NULL),
(928, 10, '', 'reservas_herramientas', 34, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-07-07 13:01:34', NULL),
(929, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 13:38:53', '::1'),
(930, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-07 15:14:59', '::1'),
(931, 10, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-07 15:15:39', NULL),
(932, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-08 12:10:30', '::1'),
(933, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-08 14:12:46', '::1'),
(934, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-08 14:20:32', '::1'),
(935, 10, '', 'reservas_herramientas', 37, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-07-08 14:23:12', NULL),
(936, 10, '', 'reservas_herramientas', 38, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-07-08 14:25:54', NULL),
(937, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-08 15:09:12', '::1'),
(938, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-08 15:22:36', '::1'),
(939, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-08 15:24:23', '::1'),
(940, 15, 'prestamo', 'prestamos', 127, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-07-08 15:25:17', NULL),
(941, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-08 15:25:39', '::1'),
(942, NULL, 'modificar', 'herramientas_consumibles', 17, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"GRASA\", \"cambios\": {\"cantidad\": {\"anterior\": 92, \"nuevo\": 91}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-08 16:32:14', NULL),
(943, 10, 'prestamo', 'prestamos', 128, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"GRASA\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 1, \"tipo\": \"consumible\"}', '2025-07-08 16:32:14', NULL),
(944, 10, '', 'reservas_herramientas', 39, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-08 16:32:14', NULL),
(945, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-09 12:15:22', '::1'),
(946, 15, 'devolucion', 'prestamos', 127, '{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}', '2025-07-09 12:17:41', NULL),
(947, NULL, 'devolucion', 'prestamos', 127, '{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"compresimetro\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":3,\"tipo\":\"no_consumible\"}', '2025-07-09 12:17:41', '::1'),
(948, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-09 12:27:34', '::1'),
(949, NULL, 'eliminar', 'aprendices', 53, '{\"descripcion\": \"Aprendiz eliminado del sistema\", \"nombre\": \"mateo arboleda diaz\", \"ficha\": \"2847431\"}', '2025-07-09 12:39:48', NULL),
(950, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-09 12:46:33', '::1'),
(951, 26, 'crear', 'usuarios', 26, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo arboleda diaz\"}', '2025-07-09 12:46:58', '::1'),
(952, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 13:54:52', '::1'),
(953, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 13:56:15', '::1'),
(954, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 13:56:37', '::1'),
(955, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 13:56:56', '::1'),
(956, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 13:57:47', '::1'),
(957, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:00:17', '::1'),
(958, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:04:14', '::1'),
(959, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-09 14:09:09', '::1'),
(960, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:15:20', '::1'),
(961, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:19:56', '::1'),
(962, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:21:31', '::1'),
(963, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:22:38', '::1'),
(964, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 14:22:50', '::1'),
(965, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 15:10:34', '::1'),
(966, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 15:19:18', '::1'),
(967, 29, 'crear', 'usuarios', 29, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"Mateo arboleda diaz\"}', '2025-07-09 15:22:50', '::1'),
(968, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-09 15:24:21', '::1'),
(969, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-09 15:59:38', '::1'),
(970, 10, '', 'reservas_herramientas', 41, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-07-09 16:08:27', NULL),
(971, 10, '', 'reservas_herramientas', 42, '{\"accion\":\"Reserva rechazada y eliminada\"}', '2025-07-09 16:08:31', NULL),
(972, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-09 16:08:54', '::1'),
(973, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 99}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-09 16:11:37', NULL),
(974, 10, 'prestamo', 'prestamos', 129, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 1, \"tipo\": \"consumible\"}', '2025-07-09 16:11:37', NULL),
(975, 10, '', 'reservas_herramientas', 43, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-09 16:11:37', NULL),
(976, NULL, 'modificar', 'herramientas_consumibles', 7, '{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 99, \"nuevo\": 97}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}', '2025-07-09 16:11:41', NULL),
(977, 10, 'prestamo', 'prestamos', 130, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"aceite\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 2, \"tipo\": \"consumible\"}', '2025-07-09 16:11:41', NULL),
(978, 10, '', 'reservas_herramientas', 40, '{\"accion\":\"Reserva aceptada y pr\\u00e9stamo creado\"}', '2025-07-09 16:11:41', NULL),
(979, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-09 16:12:39', '::1'),
(980, 14, 'crear', 'usuarios', 14, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}', '2025-07-09 16:24:28', '::1'),
(981, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-09 16:25:09', '::1'),
(982, 10, 'prestamo', 'prestamos', 131, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-07-09 16:31:37', NULL),
(983, 15, 'crear', 'usuarios', 15, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}', '2025-07-09 17:54:40', '::1'),
(984, 10, 'crear', 'usuarios', 10, '{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}', '2025-07-09 18:22:03', '::1'),
(985, 15, 'prestamo', 'prestamos', 132, '{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"compresimetro\", \"aprendiz\": \"mateo arboleda \", \"cantidad\": 2, \"tipo\": \"no_consumible\"}', '2025-07-09 18:22:40', NULL);

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

--
-- Volcado de datos para la tabla `bajas_herramientas`
--

INSERT INTO `bajas_herramientas` (`id`, `herramienta_id`, `tipo_herramienta`, `cantidad`, `motivo`, `lugar_salida`, `lugar_entrada`, `responsable`, `fecha`) VALUES
(1, 5, 'no_consumible', 1, 'dañado', 'taller diesel', 'almacen', 'mateo', '2025-06-18 10:44:51'),
(2, 16, 'no_consumible', 1, 'cambio', 'taller diesel', 'almacen', 'instructor', '2025-06-18 11:03:04'),
(3, 16, 'no_consumible', 1, 'se quebro', 'taller diesel', 'almacen', 'carlitos amparo', '2025-07-02 08:07:11');

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
(1, 'tornillos', 0, 'recargar', '3081151638', '', NULL, NULL, 'herr_682f637ad4615.png'),
(7, 'aceite', 97, 'lleno', 'HERR_680a4840ed255', '', NULL, NULL, NULL),
(12, 'tuercas', 0, 'recargar', 'CONS_61DC8A46', '', '', 10, NULL),
(13, 'PUNTILLA', 23, 'medio', 'CONS_DFE7B067', '', 'Taller', 10, NULL),
(17, 'GRASA', 91, 'lleno', 'CONS_894C3B2D', NULL, NULL, NULL, NULL);

--
-- Disparadores `herramientas_consumibles`
--
DELIMITER $$
CREATE TRIGGER `after_hconsumibles_insert` AFTER INSERT ON `herramientas_consumibles` FOR EACH ROW BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'crear',
            'herramientas_consumibles',
            NEW.id,
            JSON_OBJECT(
                'descripcion', 'Nueva herramienta consumible creada',
                'nombre', NEW.nombre,
                'cantidad', NEW.cantidad,
                'estado', NEW.estado
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_herramientas_consumibles_delete` AFTER DELETE ON `herramientas_consumibles` FOR EACH ROW BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'eliminar',
            'herramientas_consumibles',
            OLD.id,
            JSON_OBJECT(
                'descripcion', 'Herramienta consumible eliminada',
                'nombre', OLD.nombre,
                'cantidad', OLD.cantidad
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_herramientas_consumibles_update` AFTER UPDATE ON `herramientas_consumibles` FOR EACH ROW BEGIN
    DECLARE has_changes BOOLEAN DEFAULT FALSE;
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Verificar si el trigger está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        -- Comprobar cambios en cada campo relevante
        IF OLD.nombre != NEW.nombre THEN
            SET has_changes = TRUE;
        ELSEIF OLD.cantidad != NEW.cantidad THEN
            SET has_changes = TRUE;
        ELSEIF OLD.estado != NEW.estado THEN
            SET has_changes = TRUE;
        ELSEIF IFNULL(OLD.descripcion, '') != IFNULL(NEW.descripcion, '') THEN
            SET has_changes = TRUE;
        ELSEIF IFNULL(OLD.ubicacion, '') != IFNULL(NEW.ubicacion, '') THEN
            SET has_changes = TRUE;
        END IF;

        -- Solo registrar auditoría si hay cambios reales
        IF has_changes THEN
            -- Deshabilitar temporalmente otros triggers para evitar duplicados
            SET @disable_other_triggers = 1;

            INSERT INTO auditorias (
                usuario_id,
                accion,
                tabla_afectada,
                registro_id,
                detalles,
                fecha_accion,
                ip_usuario
            )
            VALUES (
                usuario_valido,
                'modificar',
                'herramientas_consumibles',
                OLD.id,
                JSON_OBJECT(
                    'descripcion', 'Modificación de material consumible',
                    'nombre', NEW.nombre,
                    'cambios', JSON_OBJECT(
                        'cantidad', JSON_OBJECT(
                            'anterior', OLD.cantidad,
                            'nuevo', NEW.cantidad
                        ),
                        'estado', JSON_OBJECT(
                            'anterior', OLD.estado,
                            'nuevo', NEW.estado
                        ),
                        'descripcion', JSON_OBJECT(
                            'anterior', IFNULL(OLD.descripcion, ''),
                            'nuevo', IFNULL(NEW.descripcion, '')
                        ),
                        'ubicacion', JSON_OBJECT(
                            'anterior', IFNULL(OLD.ubicacion, ''),
                            'nuevo', IFNULL(NEW.ubicacion, '')
                        )
                    )
                ),
                NOW(),
                NULL
            );

            -- Rehabilitar otros triggers
            SET @disable_other_triggers = NULL;
        END IF;
    END IF;
END
$$
DELIMITER ;

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
(5, 'martillo', 26, 'Activa', '4172420773', '', NULL, NULL, 'herr_682f552e99e95.png'),
(16, 'llave 8', 8, 'Activa', '3245420773', '', NULL, 10, NULL),
(27, 'compresimetro', 33, 'Activa', 'HERR_5D7D9B05', '', 'Taller', 10, NULL),
(28, 'torquimetro', 20, 'Activa', 'HERR_637E8466', '', 'Taller', 10, NULL),
(29, 'multimetro', 40, 'Activa', 'HERR_64E919D6', '', 'Taller', 10, NULL),
(30, 'gato hidraulico', 20, 'Activa', 'HERR_65CEF087', '', 'Taller', 10, NULL);

--
-- Disparadores `herramientas_no_consumibles`
--
DELIMITER $$
CREATE TRIGGER `after_herramientas_no_consumibles_delete` AFTER DELETE ON `herramientas_no_consumibles` FOR EACH ROW BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'eliminar',
            'herramientas_no_consumibles',
            OLD.id,
            JSON_OBJECT(
                'descripcion', 'Herramienta no consumible eliminada',
                'nombre', OLD.nombre,
                'cantidad', OLD.cantidad
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_hnconsumibles_insert` AFTER INSERT ON `herramientas_no_consumibles` FOR EACH ROW BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar @current_user_id si está definido
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'crear',
            'herramientas_no_consumibles',
            NEW.id,
            JSON_OBJECT(
                'descripcion', 'Nueva herramienta no consumible creada',
                'nombre', NEW.nombre,
                'cantidad', NEW.cantidad,
                'estado', NEW.estado
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;

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
(129, 1, 10, 7, 'consumible', 1, '2025-07-09 16:11:37', NULL, 'consumida', NULL),
(130, 1, 10, 7, 'consumible', 2, '2025-07-09 16:11:41', NULL, 'consumida', NULL),
(131, 47, 10, 5, 'no_consumible', 2, '2025-07-09 16:31:37', NULL, 'prestado', ''),
(132, 47, 15, 27, 'no_consumible', 2, '2025-07-09 18:22:40', NULL, 'prestado', 'eee');

--
-- Disparadores `prestamos`
--
DELIMITER $$
CREATE TRIGGER `after_prestamos_insert` AFTER INSERT ON `prestamos` FOR EACH ROW BEGIN
    DECLARE herramienta_nombre VARCHAR(100);
    DECLARE aprendiz_nombre VARCHAR(100);
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Validar usuario_id si existe
    IF NEW.usuario_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = NEW.usuario_id
        LIMIT 1;
    END IF;

    -- Obtener nombre de la herramienta
    IF NEW.herramienta_tipo = 'consumible' THEN
        SELECT nombre INTO herramienta_nombre
        FROM herramientas_consumibles
        WHERE id = NEW.herramienta_id;
    ELSE
        SELECT nombre INTO herramienta_nombre
        FROM herramientas_no_consumibles
        WHERE id = NEW.herramienta_id;
    END IF;

    -- Obtener nombre del aprendiz
    SELECT nombre INTO aprendiz_nombre
    FROM aprendices
    WHERE id = NEW.id_aprendiz;

    -- Insertar registro en auditorias solo si no está deshabilitado
    IF @disable_audit_trigger IS NULL THEN
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            usuario_valido,
            'prestamo',
            'prestamos',
            NEW.id,
            JSON_OBJECT(
                'descripcion', 'Nuevo préstamo registrado',
                'herramienta', herramienta_nombre,
                'aprendiz', aprendiz_nombre,
                'cantidad', NEW.cantidad,
                'tipo', NEW.herramienta_tipo
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_prestamos_update` AFTER UPDATE ON `prestamos` FOR EACH ROW BEGIN
    DECLARE herramienta_nombre VARCHAR(100);
    DECLARE aprendiz_nombre VARCHAR(100);
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Solo registrar si el estado cambió a 'devuelto'
    IF OLD.estado != NEW.estado AND NEW.estado = 'devuelto' THEN
        -- Validar usuario_id si existe
        IF NEW.usuario_id IS NOT NULL THEN
            SELECT id INTO usuario_valido
            FROM usuarios
            WHERE id = NEW.usuario_id
            LIMIT 1;
        END IF;

        -- Obtener nombre de la herramienta
        IF NEW.herramienta_tipo = 'consumible' THEN
            SELECT nombre INTO herramienta_nombre
            FROM herramientas_consumibles
            WHERE id = NEW.herramienta_id;
        ELSE
            SELECT nombre INTO herramienta_nombre
            FROM herramientas_no_consumibles
            WHERE id = NEW.herramienta_id;
        END IF;

        -- Obtener nombre del aprendiz
        SELECT nombre INTO aprendiz_nombre
        FROM aprendices
        WHERE id = NEW.id_aprendiz;

        -- Insertar registro en auditorias solo si no está deshabilitado
        IF @disable_audit_trigger IS NULL THEN
            INSERT INTO auditorias (
                usuario_id,
                accion,
                tabla_afectada,
                registro_id,
                detalles,
                fecha_accion,
                ip_usuario
            )
            VALUES (
                usuario_valido,
                'devolucion',
                'prestamos',
                NEW.id,
                JSON_OBJECT(
                    'descripcion', 'Devolución de herramienta',
                    'herramienta', herramienta_nombre,
                    'aprendiz', aprendiz_nombre,
                    'cantidad', NEW.cantidad,
                    'tipo', NEW.herramienta_tipo
                ),
                NOW(),
                NULL
            );
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_herramienta_consumible` BEFORE INSERT ON `prestamos` FOR EACH ROW BEGIN
    IF NEW.herramienta_tipo = 'consumible' THEN
        IF NOT EXISTS (SELECT 1 FROM `herramientas_consumibles` WHERE id = NEW.herramienta_id) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Herramienta consumible no existe';
        END IF;
    ELSE
        IF NOT EXISTS (SELECT 1 FROM `herramientas_no_consumibles` WHERE id = NEW.herramienta_id) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Herramienta no consumible no existe';
        END IF;
    END IF;
END
$$
DELIMITER ;

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

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `id_aprendiz`, `observaciones`, `fecha_reporte`, `resuelto`) VALUES
(3, 2, 'Herramientas pendientes: martillo (cantidad: 20)', '2025-03-27 11:36:23', 1),
(5, 2, 'Herramientas pendientes: martillo (cantidad: 3) | Total préstamos pendientes: 1', '2025-04-01 09:09:58', 1),
(8, 4, 'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1', '2025-04-17 12:10:57', 1),
(9, 4, 'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1', '2025-04-17 13:35:05', 0),
(13, 4, 'Herramientas pendientes: tornillos (cantidad: 60), tornillos (cantidad: 80) | Total préstamos pendientes: 4', '2025-06-05 11:03:57', 0),
(21, 4, 'Herramientas pendientes: tornillos (cantidad: 60), tornillos (cantidad: 80) | Total préstamos pendientes: 4', '2025-06-18 08:26:27', 0),
(24, 30, 'Herramientas pendientes: torquimetro (cantidad: 5) | Total préstamos pendientes: 1', '2025-06-18 08:26:27', 0);

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
(12, 7, 'consumible', 'Mateo arboleda diaz', '2847431', '2025-06-11', '2025-06-11 15:28:54', 'aprobada', 30),
(13, 16, 'no_consumible', 'mateo arboleda diaz', '2847431', '2025-06-11', '2025-06-11 15:29:17', 'aprobada', 5),
(14, 1, 'consumible', 'mateo qrboleda', '2847431', '2025-06-12', '2025-06-12 12:55:09', 'aprobada', 50),
(15, 12, 'consumible', 'mateo qrboleda', '2847431', '2025-06-12', '2025-06-12 12:56:27', 'aprobada', 40),
(16, 1, 'consumible', 'mateo arboleda', '2847431', '2025-06-12', '2025-06-12 14:09:06', 'aprobada', 20),
(18, 1, 'consumible', 'mateo arboleda', '2847431', '2025-06-12', '2025-06-12 16:02:34', 'aprobada', 23),
(19, 12, 'consumible', 'mateo arboleda diaz', '2847431', '2025-06-16', '2025-06-16 12:35:19', 'aprobada', 50),
(22, 5, 'no_consumible', 'JUNITOS PARRA', '2847431', '2025-06-16', '2025-06-16 13:36:58', 'aprobada', 6),
(23, 16, 'no_consumible', 'jeronimo sanchez holgin', '2847411', '2025-06-16', '2025-06-16 14:04:35', 'aprobada', 2),
(24, 28, 'no_consumible', 'alan brito', '2847431', '2025-06-16', '2025-06-16 14:05:20', 'aprobada', 5),
(25, 30, 'no_consumible', 'SANTIAGO GIRTALOD', '2847431', '2025-06-16', '2025-06-16 14:07:15', 'aprobada', 3),
(26, 16, 'no_consumible', 'natalia cardona', '2847431', '2025-06-16', '2025-06-16 14:09:22', 'aprobada', 5),
(27, 1, 'consumible', 'mateo arboleda diaz', '2847431', '2025-06-17', '2025-06-17 14:12:48', 'aprobada', 2),
(28, 5, 'no_consumible', 'mateo arboleda ', '2847431', '2025-06-18', '2025-06-18 13:18:37', 'aprobada', 2),
(29, 5, 'no_consumible', 'julio jaramilo', '2847431', '2025-06-24', '2025-06-24 15:38:14', 'aprobada', 2),
(31, 5, 'no_consumible', 'mateo arboleda', '2847431', '2025-06-25', '2025-06-25 13:14:50', 'aprobada', 1),
(33, 17, 'consumible', 'alan brito', '2847431', '2025-06-25', '2025-07-02 13:00:29', 'aprobada', 1),
(35, 17, 'consumible', 'jromstyle', '2847431', '2025-07-06', '2025-07-07 12:46:46', 'aprobada', 7),
(36, 13, 'consumible', 'Santiago Girtalod', '2847431', '2025-07-07', '2025-07-07 12:57:26', 'aprobada', 7),
(38, 7, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-02', '2025-07-08 14:25:45', 'rechazada', 1),
(39, 17, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-02', '2025-07-08 15:08:56', 'aprobada', 1),
(40, 7, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-08', '2025-07-08 15:23:53', 'aprobada', 2),
(41, 5, 'no_consumible', '1', '2847431', '2025-07-03', '2025-07-08 17:01:11', 'rechazada', 1),
(42, 7, 'consumible', '1', '2847431', '2025-07-03', '2025-07-09 15:59:25', 'rechazada', 1),
(43, 7, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-03', '2025-07-09 16:11:29', 'aprobada', 1),
(44, 7, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-03', '2025-07-09 16:24:34', 'pendiente', 1),
(45, 13, 'consumible', 'mateo arboleda diaz', '2847431', '2025-07-03', '2025-07-09 16:24:56', 'pendiente', 1);

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
(10, 'admin', '$2y$10$ac7wfUxtZrBtmr2upUmqdOM.9ZAus9/YtWm1UWZLTv22a4QBQAuxK', 'administrador', NULL, '+573008941957', 'c503cb1aae94bd0fd58e78e78659e404f2894da9a6ccc2d241a5de07b06a3b77', '2025-08-08 20:22:03', '2025-04-24 14:25:07', 1),
(14, 'aprendiz', '$2y$10$XzQDN6AkvD8UIRUOP.Qfcuhe2HGVAaXYZnI6DAm/U7tIKfS.2sFny', 'aprendiz', NULL, '+57300546333', 'd309639d2c50ec858e23652aec92592758faa29adef370a7c5cfcb0964b4cb42', '2025-08-08 14:27:34', '2025-04-26 16:23:53', 1),
(15, 'mateo', '$2y$10$5BJu73AJ8q9dJTRY9GnAjuT17uCBxhp9m.MDe35XWkHZ3eAxVO40W', 'almacenista', NULL, '+57300546332', '0be0db5c7adac71e5e6e8e9d42ec7f6f9951ee56a7f25ebe009cd8e30fab1a65', '2025-08-08 16:09:09', '2025-06-09 16:21:37', 1),
(17, 'invi', '$2y$10$eigRn.VjtAcqomcToz5OM..nx3AJTtWO53lYhyPYqSS//PJFJDjIS', 'aprendiz', NULL, '+573008941957', '628fd185255483a5942c80826a5429fbfeb1a63f8908cdbb1fefdb8f27b2563a', '2025-07-12 15:13:43', '2025-06-11 12:56:49', 0),
(18, 'luis', '$2y$10$S9GXjAdzzT1gyl7nZyOJtusrgBFv2Aw12w0vfe5qTbxkYv4Jx0dzq', 'aprendiz', NULL, '+57300546888', '787f2284d52696e7272eaf09e3139dcaa231b7c557b3f6fc9f3f1e1b92ace768', '2025-07-12 18:49:55', '2025-06-12 16:49:43', 1),
(19, 'alan brito', '$2y$10$W3rXtBW0p42ovbmJI0U1SOAOVVqghF8no8FOknbFYx4BSDZ3aseNC', 'aprendiz', NULL, '+57300546888', NULL, NULL, '2025-06-16 12:06:30', 1),
(20, 'jhon doe', '$2y$10$CCAPZhMBgglL7URdjpiw6.BQlwPzdo/GbOg7zjU4Kfk/8HTkqe4ny', 'aprendiz', NULL, '+57300546332', NULL, NULL, '2025-06-16 12:06:53', 1),
(21, 'natalia cardona', '$2y$10$lGYCiOe0H4SZoo57dkgIHuXp4drS4k6h48ENjJN3ySNubdy7pSgO.', 'aprendiz', NULL, '+57300546332', '055ecbdd721d21022a3d9349ea1e8d53207c4d32bdd3d78049fbce0be8ed89a6', '2025-07-16 16:08:37', '2025-06-16 14:08:25', 1),
(22, 'jhon', '$2y$10$VFtlzMlb7bba8YGjTK2OAeL505da8JPoAbjrpdqcIZ8jYXD/h9AAu', 'aprendiz', NULL, '+57300546333', '8e4b1ba9fb0f039260b3b14f5da62576ab1e0f3d51663b7f7d3d44352d9f714d', '2025-07-24 15:01:38', '2025-06-24 13:01:30', 1),
(23, 'jromstyle', '$2y$10$GYKPRNAbSWT3gVc0idVvUeXRwkwntqNFmNOaCPfeIimvBgMsJOMa6', 'aprendiz', NULL, '+573001026551', NULL, NULL, '2025-07-07 12:41:07', 1),
(24, 'san', '$2y$10$YArzv0RWzxbi8vTSm8GsNORT5LvAyNyofTuiN.TPAQxAdkslWadyy', 'aprendiz', NULL, '+573014946732', NULL, NULL, '2025-07-07 12:54:20', 1),
(29, 'Mateo arboleda diaz', '$2y$10$aixIVKLA0/5gWhK9tzuwluZbmU4MaiMZWH0EFbTFvq0CEGPnf3PKa', 'aprendiz', 1, '+57300546332', '90855205cb5e2904c22a6c34cb4521c15cc4fc5864976cc737f3bfff58eee46f', '2025-08-08 17:22:50', '2025-07-09 13:43:45', 1);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `before_usuarios_update` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.activo = 0 AND OLD.activo = 1 THEN
        -- Registrar auditoría cuando se desactiva un usuario
        INSERT INTO auditorias (
            usuario_id,
            accion,
            tabla_afectada,
            registro_id,
            detalles,
            fecha_accion,
            ip_usuario
        )
        VALUES (
            @current_user_id,
            'desactivar',
            'usuarios',
            NEW.id,
            JSON_OBJECT(
                'descripcion', 'Desactivación de usuario',
                'usuario', NEW.usuario,
                'rol', NEW.rol
            ),
            NOW(),
            NULL
        );
    END IF;
END
$$
DELIMITER ;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=986;

--
-- AUTO_INCREMENT de la tabla `bajas_herramientas`
--
ALTER TABLE `bajas_herramientas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `herramientas_consumibles`
--
ALTER TABLE `herramientas_consumibles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `herramientas_no_consumibles`
--
ALTER TABLE `herramientas_no_consumibles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `reservas_herramientas`
--
ALTER TABLE `reservas_herramientas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `herramientas_consumibles`
--
ALTER TABLE `herramientas_consumibles`
  ADD CONSTRAINT `fk_hc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `herramientas_no_consumibles`
--
ALTER TABLE `herramientas_no_consumibles`
  ADD CONSTRAINT `fk_hnc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_prestamos_aprendiz` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id`),
  ADD CONSTRAINT `fk_prestamos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
