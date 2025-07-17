-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: diesel
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `diesel`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `diesel` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `diesel`;

--
-- Table structure for table `aprendices`
--

DROP TABLE IF EXISTS `aprendices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aprendices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `ficha` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aprendices`
--

LOCK TABLES `aprendices` WRITE;
/*!40000 ALTER TABLE `aprendices` DISABLE KEYS */;
INSERT INTO `aprendices` VALUES (1,'mateo arboleda diaz','2847431',1),(2,'jeronimo sanchez holgin','2847411',1),(3,'martin perez gonzales','55545454',1),(4,'benito camelo suave','55545454',1),(5,'susanita porras','55545454',1),(6,'lina gonzales','55545454',1);
/*!40000 ALTER TABLE `aprendices` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_aprendices_delete` AFTER DELETE ON `aprendices` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `auditorias`
--

DROP TABLE IF EXISTS `auditorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` enum('crear','modificar','eliminar','prestamo','devolucion','cambio_estado') NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `detalles` text DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_usuario` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_auditoria_usuario` (`usuario_id`),
  CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditorias`
--

LOCK TABLES `auditorias` WRITE;
/*!40000 ALTER TABLE `auditorias` DISABLE KEYS */;
INSERT INTO `auditorias` VALUES (20,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-23 22:19:30','::1'),(21,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-23 22:49:54','::1'),(22,NULL,'crear','usuarios',8,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"espinaca\"}','2025-04-23 23:00:37','::1'),(23,NULL,'prestamo','prestamos',51,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 4, \"tipo\": \"no_consumible\"}','2025-04-23 23:01:07',NULL),(25,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-23 23:02:22','::1'),(27,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 03:20:37','::1'),(28,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 03:20:39','::1'),(29,NULL,'crear','usuarios',8,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"espinaca\"}','2025-04-24 03:26:20','::1'),(30,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 03:27:09','::1'),(34,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 12:41:43','::1'),(38,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 13:16:33','::1'),(40,NULL,'prestamo','prestamos',52,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-24 13:27:41',NULL),(42,NULL,'prestamo','prestamos',53,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 50, \"tipo\": \"consumible\"}','2025-04-24 13:29:13',NULL),(44,NULL,'crear','usuarios',9,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"alberto\"}','2025-04-24 13:45:53','::1'),(45,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 13:46:53','::1'),(54,NULL,'crear','usuarios',9,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"alberto\"}','2025-04-24 13:54:30','::1'),(59,NULL,'prestamo','prestamos',60,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-24 14:02:54',NULL),(60,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 13, \"cantidad_nueva\": 8, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-24 14:02:55',NULL),(61,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 14:03:10','::1'),(66,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 50, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}','2025-04-24 14:12:20',NULL),(67,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 200, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}','2025-04-24 14:13:32',NULL),(68,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":50,\"cantidad_nueva\":\"200\",\"estado_anterior\":\"medio\",\"estado_nuevo\":\"medio\"}','2025-04-24 14:13:32','::1'),(69,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}','2025-04-24 14:13:33',NULL),(70,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 14:13:58','::1'),(71,NULL,'crear','herramientas_no_consumibles',15,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"pala\", \"cantidad\": 30, \"estado\": \"\"}','2025-04-24 14:18:05',NULL),(72,NULL,'crear','herramientas_consumibles',6,'{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"puntilla\", \"cantidad\": 100, \"estado\": \"lleno\"}','2025-04-24 14:18:30',NULL),(73,NULL,'crear','herramientas_consumibles',7,'{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"aceite\", \"cantidad\": 20, \"estado\": \"lleno\"}','2025-04-24 14:18:40',NULL),(74,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}','2025-04-24 14:18:42',NULL),(79,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 14:25:42','::1'),(80,10,'eliminar','herramientas_consumibles',6,'{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"puntilla\",\"cantidad\":100}','2025-04-24 14:25:56','::1'),(81,10,'eliminar','herramientas_consumibles',6,'{\"descripcion\": \"Herramienta consumible eliminada\", \"nombre\": \"puntilla\", \"cantidad\": 100}','2025-04-24 14:25:56',NULL),(82,10,'crear','herramientas_no_consumibles',16,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"llave 8\", \"cantidad\": 50, \"estado\": \"\"}','2025-04-24 14:36:43',NULL),(83,10,'eliminar','herramientas_no_consumibles',15,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"pala\",\"cantidad\":30}','2025-04-24 14:37:12','::1'),(84,10,'eliminar','herramientas_no_consumibles',15,'{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"pala\", \"cantidad\": 30}','2025-04-24 14:37:12',NULL),(85,10,'crear','herramientas_no_consumibles',17,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"rosas\", \"cantidad\": 21, \"estado\": \"\"}','2025-04-24 14:44:00',NULL),(86,10,'eliminar','herramientas_no_consumibles',17,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"rosas\",\"cantidad\":21}','2025-04-24 14:44:38','::1'),(87,10,'eliminar','herramientas_no_consumibles',17,'{\"descripcion\": \"Herramienta no consumible eliminada\", \"nombre\": \"rosas\", \"cantidad\": 21}','2025-04-24 14:44:38',NULL),(88,10,'crear','herramientas_no_consumibles',18,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"luoa\", \"cantidad\": 23, \"estado\": \"\"}','2025-04-24 14:56:56',NULL),(89,10,'modificar','herramientas_no_consumibles',18,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"luoa\", \"cantidad_anterior\": 23, \"cantidad_nueva\": 244, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 14:57:11',NULL),(90,10,'modificar','herramientas_no_consumibles',18,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"luoa\",\"cantidad_anterior\":23,\"cantidad_nueva\":\"244\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 14:57:11','::1'),(91,10,'eliminar','herramientas_no_consumibles',18,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"luoa\",\"cantidad\":244}','2025-04-24 14:57:18','::1'),(92,10,'crear','herramientas_no_consumibles',19,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"sds\", \"cantidad\": 2, \"estado\": \"\"}','2025-04-24 15:04:17',NULL),(93,10,'modificar','herramientas_no_consumibles',19,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2, \"cantidad_nueva\": 222, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:04:25',NULL),(94,10,'modificar','herramientas_no_consumibles',19,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2, \"cantidad_nueva\": 222, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:04:25',NULL),(95,10,'modificar','herramientas_no_consumibles',19,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"sds\",\"cantidad_anterior\":2,\"cantidad_nueva\":\"222\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 15:04:25','::1'),(96,10,'eliminar','herramientas_no_consumibles',19,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"sds\",\"cantidad\":222}','2025-04-24 15:04:31','::1'),(97,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:04:35',NULL),(98,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:04:35',NULL),(99,10,'crear','herramientas_consumibles',8,'{\"descripcion\": \"Nueva herramienta consumible creada\", \"nombre\": \"sds\", \"cantidad\": 22, \"estado\": \"lleno\"}','2025-04-24 15:04:46',NULL),(100,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:04:47',NULL),(101,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:04:47',NULL),(102,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 22, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}','2025-04-24 15:04:47',NULL),(103,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 22, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}','2025-04-24 15:04:47',NULL),(104,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}','2025-04-24 15:04:53',NULL),(105,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 22, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}','2025-04-24 15:04:53',NULL),(106,10,'modificar','herramientas_consumibles',8,'{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"sds\",\"cantidad_anterior\":22,\"cantidad_nueva\":\"2222\",\"estado_anterior\":\"medio\",\"estado_nuevo\":\"medio\"}','2025-04-24 15:04:53','::1'),(107,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:04:55',NULL),(108,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:04:55',NULL),(109,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2222, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:04:55',NULL),(110,10,'modificar','herramientas_consumibles',8,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"sds\", \"cantidad_anterior\": 2222, \"cantidad_nueva\": 2222, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:04:55',NULL),(111,10,'eliminar','herramientas_consumibles',8,'{\"descripcion\":\"Herramienta consumible eliminada\",\"nombre\":\"sds\",\"cantidad\":2222}','2025-04-24 15:04:59','::1'),(112,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:05:00',NULL),(113,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:05:00',NULL),(114,10,'crear','herramientas_no_consumibles',20,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"lllave\", \"cantidad\": 20, \"estado\": \"\"}','2025-04-24 15:14:39',NULL),(115,10,'modificar','herramientas_no_consumibles',20,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"lllave\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 21, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:15:06',NULL),(116,10,'modificar','herramientas_no_consumibles',20,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"lllave\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 21, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:15:06',NULL),(117,10,'modificar','herramientas_no_consumibles',20,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"lllave\",\"cantidad_anterior\":20,\"cantidad_nueva\":\"21\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 15:15:06','::1'),(118,10,'eliminar','herramientas_no_consumibles',20,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"lllave\",\"cantidad\":21}','2025-04-24 15:15:46','::1'),(119,10,'modificar','herramientas_no_consumibles',16,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 50, \"cantidad_nueva\": 77, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:24:59',NULL),(120,10,'modificar','herramientas_no_consumibles',16,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"llave 8\",\"cantidad_anterior\":50,\"cantidad_nueva\":\"77\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 15:24:59','::1'),(121,10,'modificar','herramientas_no_consumibles',16,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 77, \"cantidad_nueva\": 50, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-04-24 15:36:29',NULL),(122,10,'modificar','herramientas_no_consumibles',16,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"llave 8\",\"cantidad_anterior\":77,\"cantidad_nueva\":\"50\",\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 15:36:29','::1'),(123,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:41:40',NULL),(124,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 20, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:41:40',NULL),(125,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 20, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 15:41:50',NULL),(126,10,'modificar','herramientas_consumibles',7,'{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"aceite\",\"cantidad_anterior\":20,\"cantidad_nueva\":\"100\",\"estado_anterior\":\"recargar\",\"estado_nuevo\":\"recargar\"}','2025-04-24 15:41:50','::1'),(127,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:41:51',NULL),(128,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:41:51',NULL),(129,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 200, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:53:15',NULL),(130,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:53:15',NULL),(131,10,'prestamo','prestamos',61,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 100, \"tipo\": \"consumible\"}','2025-04-24 15:54:01',NULL),(132,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 200, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:54:01',NULL),(133,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:54:11',NULL),(134,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:54:11',NULL),(135,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:55:32',NULL),(136,10,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":100,\"cantidad_nueva\":\"1000\",\"estado_anterior\":\"lleno\",\"estado_nuevo\":\"lleno\"}','2025-04-24 15:55:32','::1'),(137,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:55:34',NULL),(138,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 15:55:34',NULL),(139,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 1000, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 16:16:34',NULL),(140,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 16:16:34',NULL),(141,10,'crear','herramientas_no_consumibles',21,'{\"descripcion\": \"Nueva herramienta no consumible creada\", \"nombre\": \"lupa\", \"cantidad\": 15, \"estado\": \"\"}','2025-04-24 16:17:01',NULL),(142,10,'modificar','herramientas_no_consumibles',21,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"lupa\",\"cantidad_anterior\":15,\"cantidad_nueva\":20,\"estado_anterior\":\"\",\"estado_nuevo\":\"\"}','2025-04-24 16:17:28','::1'),(143,10,'eliminar','herramientas_no_consumibles',21,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"lupa\",\"cantidad\":20}','2025-04-24 16:17:52','::1'),(144,10,'prestamo','prestamos',62,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"tornillos\", \"aprendiz\": \"jeronimo sanchez holgin\", \"cantidad\": 233, \"tipo\": \"consumible\"}','2025-04-24 16:19:31',NULL),(145,10,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 1000, \"cantidad_nueva\": 767, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 16:19:31',NULL),(146,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 17:01:01','::1'),(152,NULL,'modificar','usuarios',7,'{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}','2025-04-24 17:06:19',NULL),(153,NULL,'modificar','usuarios',7,'{\"descripcion\":\"Cambio de rol de usuario\",\"usuario_id\":7,\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}','2025-04-24 17:06:19','::1'),(154,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 17:06:57','::1'),(173,NULL,'crear','usuarios',7,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"mateo\"}','2025-04-24 17:45:50','::1'),(174,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\":\"Herramienta no_consumible modificada\",\"nombre\":\"martillo\",\"cantidad_anterior\":17,\"cantidad_nueva\":27,\"estado_anterior\":\"Activa\",\"estado_nuevo\":\"Activa\"}','2025-04-24 17:46:10','::1'),(175,NULL,'prestamo','prestamos',63,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 3, \"tipo\": \"no_consumible\"}','2025-04-24 17:48:06',NULL),(176,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 24, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-24 17:48:06',NULL),(177,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 411, \"cantidad_nueva\": 411, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:50:09',NULL),(178,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:50:09',NULL),(179,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Herramienta consumible modificada\",\"nombre\":\"tornillos\",\"cantidad_anterior\":411,\"cantidad_nueva\":41,\"estado_anterior\":\"lleno\",\"estado_nuevo\":\"lleno\"}','2025-04-24 17:50:16','::1'),(180,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"medio\"}','2025-04-24 17:50:17',NULL),(181,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:50:17',NULL),(182,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}','2025-04-24 17:57:19',NULL),(183,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:57:19',NULL),(184,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 41, \"cantidad_nueva\": 41, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"medio\"}','2025-04-24 17:59:12',NULL),(185,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:59:12',NULL),(186,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":41,\"nuevo\":\"10\"}}}','2025-04-24 17:59:20','::1'),(187,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"medio\", \"estado_nuevo\": \"recargar\"}','2025-04-24 17:59:21',NULL),(188,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 17:59:21',NULL),(189,NULL,'modificar','herramientas_no_consumibles',16,'{\"descripcion\":\"Modificaci\\u00f3n de herramienta\",\"nombre\":\"llave 8\",\"cambios\":{\"cantidad\":{\"anterior\":50,\"nuevo\":\"10\"}}}','2025-04-24 18:00:12','::1'),(190,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 18:08:24',NULL),(191,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:08:24',NULL),(192,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":10,\"nuevo\":\"100\"}}}','2025-04-24 18:08:32','::1'),(193,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:08:34',NULL),(194,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:08:34',NULL),(195,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:13',NULL),(196,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:13',NULL),(197,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:20',NULL),(198,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:20',NULL),(199,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:37',NULL),(200,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:37',NULL),(201,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":100,\"nuevo\":\"10\"}}}','2025-04-24 18:13:44','::1'),(202,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}','2025-04-24 18:13:45',NULL),(203,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:13:45',NULL),(204,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}','2025-04-24 18:17:14',NULL),(205,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:17:14',NULL),(206,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":10,\"nuevo\":\"100\"}}}','2025-04-24 18:17:25','::1'),(207,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:17:26',NULL),(208,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:17:26',NULL),(209,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:29:13',NULL),(210,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"tornillos\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}','2025-04-24 18:29:15',NULL),(211,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Herramienta consumible modificada\", \"nombre\": \"aceite\", \"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}','2025-04-24 18:29:15',NULL),(212,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}','2025-04-24 18:48:08',NULL),(213,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:48:08',NULL),(214,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}','2025-04-24 18:48:18',NULL),(215,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:48:20',NULL),(216,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:48:20',NULL),(217,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:55:50',NULL),(218,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:55:50',NULL),(219,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:56:00',NULL),(220,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cambios\":{\"cantidad\":{\"anterior\":101,\"nuevo\":\"10\"}}}','2025-04-24 18:56:00','::1'),(221,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"recargar\"}}','2025-04-24 18:56:01',NULL),(222,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 18:56:01',NULL),(223,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}','2025-04-24 19:07:37',NULL),(224,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 19:07:37',NULL),(225,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 10, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"recargar\"}}','2025-04-24 19:07:54',NULL),(226,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\":\"Modificaci\\u00f3n de material consumible\",\"nombre\":\"tornillos\",\"cantidad_anterior\":10,\"cantidad_nueva\":\"101\"}','2025-04-24 19:07:54','::1'),(227,NULL,'modificar','herramientas_consumibles',1,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"tornillos\", \"cambios\": {\"cantidad_anterior\": 101, \"cantidad_nueva\": 101, \"estado_anterior\": \"recargar\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 19:07:55',NULL),(228,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad_anterior\": 100, \"cantidad_nueva\": 100, \"estado_anterior\": \"lleno\", \"estado_nuevo\": \"lleno\"}}','2025-04-24 19:07:55',NULL),(229,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 5}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-24 19:13:38',NULL),(230,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 5, \"nuevo\": 5}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-24 19:13:39',NULL),(231,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 5, \"nuevo\": 66}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-24 19:17:47',NULL),(232,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 66, \"nuevo\": 66}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-24 19:17:48',NULL),(233,NULL,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 6, \"nuevo\": 6}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-24 19:24:55',NULL),(234,NULL,'crear','herramientas_consumibles',11,'{\"descripcion\":\"Creaci\\u00f3n de material consumible\",\"nombre\":\"dsad\",\"cantidad\":\"12\",\"estado\":\"lleno\"}','2025-04-24 19:48:34','::1'),(235,NULL,'modificar','herramientas_consumibles',11,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"dsad\", \"cambios\": {\"cantidad\": {\"anterior\": 12, \"nuevo\": 12}, \"estado\": {\"anterior\": \"lleno\", \"nuevo\": \"recargar\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"Taller\", \"nuevo\": \"Taller\"}}}','2025-04-24 19:48:35',NULL),(236,NULL,'crear','herramientas_no_consumibles',22,'{\"descripcion\":\"Creaci\\u00f3n de herramienta\",\"nombre\":\"sad\",\"cantidad\":\"12\"}','2025-04-24 19:48:46','::1'),(237,NULL,'eliminar','herramientas_no_consumibles',22,'{\"descripcion\":\"Herramienta no_consumible eliminada\",\"nombre\":\"sad\",\"cantidad\":12}','2025-04-24 19:48:56','::1'),(239,NULL,'devolucion','prestamos',60,'{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":5,\"tipo\":\"no_consumible\"}','2025-04-24 20:01:25','::1'),(241,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:20:47','::1'),(242,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:22:18','::1'),(243,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:29:42','::1'),(244,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:33:14','::1'),(245,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:35:23','::1'),(246,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-24 21:52:18','::1'),(247,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-26 15:10:42','::1'),(248,10,'modificar','usuarios',7,'{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}','2025-04-26 15:15:31',NULL),(249,10,'modificar','usuarios',7,'{\"descripcion\":\"Cambio de rol de usuario\",\"nuevo_rol\":\"usuario\"}','2025-04-26 15:16:09',NULL),(250,10,'modificar','usuarios',7,'{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"mateo\",\"rol_anterior\":\"usuario\",\"rol_nuevo\":\"administrador\"}','2025-04-26 15:20:41','::1'),(251,10,'eliminar','usuarios',11,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}','2025-04-26 15:20:50','::1'),(253,10,'prestamo','prestamos',64,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}','2025-04-26 15:23:50',NULL),(254,10,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 29, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 15:23:50',NULL),(256,10,'devolucion','prestamos',64,'{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":2,\"tipo\":\"no_consumible\"}','2025-04-26 15:24:22','::1'),(258,10,'devolucion','prestamos',63,'{\"descripcion\":\"Devoluci\\u00f3n de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":3,\"tipo\":\"no_consumible\"}','2025-04-26 15:24:26','::1'),(259,NULL,'crear','usuarios',12,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}','2025-04-26 15:34:25','::1'),(263,10,'eliminar','usuarios',12,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}','2025-04-26 15:51:34','::1'),(267,10,'modificar','usuarios',5,'{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"carlos loaiza\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}','2025-04-26 15:52:57','::1'),(273,10,'eliminar','usuarios',9,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"alberto\",\"rol\":\"usuario\"}','2025-04-26 15:56:45','::1'),(276,10,'eliminar','usuarios',7,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"mateo\",\"rol\":\"administrador\"}','2025-04-26 15:56:59','::1'),(282,10,'eliminar','usuarios',8,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"espinaca\",\"rol\":\"usuario\"}','2025-04-26 15:59:01','::1'),(311,10,'eliminar','usuarios',5,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"carlos loaiza\",\"rol\":\"usuario\"}','2025-04-26 16:18:19','::1'),(312,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 16:18:19',NULL),(313,5,'eliminar','usuarios',5,'{\"descripcion\": \"Eliminación de usuario\", \"usuario\": \"carlos loaiza\", \"rol\": \"usuario\"}','2025-04-26 16:18:19',NULL),(314,10,'modificar','usuarios',13,'{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"usuario\",\"rol_nuevo\":\"administrador\"}','2025-04-26 16:21:23','::1'),(315,10,'modificar','usuarios',13,'{\"descripcion\":\"Cambio de rol de usuario\",\"usuario\":\"aprendiz\",\"rol_anterior\":\"administrador\",\"rol_nuevo\":\"usuario\"}','2025-04-26 16:21:27','::1'),(316,10,'eliminar','usuarios',13,'{\"descripcion\":\"Eliminaci\\u00f3n de usuario\",\"usuario\":\"aprendiz\",\"rol\":\"usuario\"}','2025-04-26 16:21:43','::1'),(317,5,'eliminar','usuarios',13,'{\"descripcion\": \"Eliminación de usuario\", \"usuario\": \"aprendiz\", \"rol\": \"usuario\"}','2025-04-26 16:21:43',NULL),(318,14,'prestamo','prestamos',65,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-26 16:25:20',NULL),(319,14,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 16:25:20',NULL),(332,14,'devolucion','prestamos',65,'{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-26 17:09:16',NULL),(333,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 17:09:16',NULL),(334,NULL,'devolucion','prestamos',65,'{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}','2025-04-26 17:09:16','::1'),(335,10,'prestamo','prestamos',66,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-26 17:10:35',NULL),(336,10,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 27, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 17:10:35',NULL),(337,10,'devolucion','prestamos',66,'{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"mateo arboleda diaz\", \"cantidad\": 5, \"tipo\": \"no_consumible\"}','2025-04-26 17:11:16',NULL),(338,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 27, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-26 17:11:16',NULL),(339,NULL,'devolucion','prestamos',66,'{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"mateo arboleda diaz\",\"cantidad\":5,\"tipo\":\"no_consumible\"}','2025-04-26 17:11:16','::1'),(340,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-29 12:33:03','::1'),(341,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-30 15:23:09','::1'),(342,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-30 15:26:05','::1'),(343,14,'crear','usuarios',14,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}','2025-04-30 15:38:24','::1'),(344,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-04-30 16:07:35','::1'),(345,10,'prestamo','prestamos',67,'{\"descripcion\": \"Nuevo préstamo registrado\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}','2025-04-30 16:09:19',NULL),(346,10,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 32, \"cantidad_nueva\": 30, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-30 16:09:19',NULL),(347,10,'devolucion','prestamos',67,'{\"descripcion\": \"Devolución de herramienta\", \"herramienta\": \"martillo\", \"aprendiz\": \"martin perez gonzales\", \"cantidad\": 2, \"tipo\": \"no_consumible\"}','2025-04-30 16:09:44',NULL),(348,NULL,'modificar','herramientas_no_consumibles',5,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"martillo\", \"cantidad_anterior\": 30, \"cantidad_nueva\": 32, \"estado_anterior\": \"Activa\", \"estado_nuevo\": \"Activa\"}','2025-04-30 16:09:44',NULL),(349,NULL,'devolucion','prestamos',67,'{\"descripcion\":\"Devolución de herramienta no consumible\",\"herramienta\":\"martillo\",\"aprendiz\":\"martin perez gonzales\",\"cantidad\":2,\"tipo\":\"no_consumible\"}','2025-04-30 16:09:44','::1'),(350,10,'modificar','herramientas_consumibles',7,'{\"descripcion\": \"Modificación de material consumible\", \"nombre\": \"aceite\", \"cambios\": {\"cantidad\": {\"anterior\": 100, \"nuevo\": 100}, \"estado\": {\"anterior\": \"recargar\", \"nuevo\": \"lleno\"}, \"descripcion\": {\"anterior\": \"\", \"nuevo\": \"\"}, \"ubicacion\": {\"anterior\": \"\", \"nuevo\": \"\"}}}','2025-04-30 16:10:33',NULL),(351,14,'crear','usuarios',14,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}','2025-04-30 16:11:44','::1'),(352,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 12:46:54','::1'),(353,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 12:46:59','::1'),(354,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 13:32:49','10.2.22.76'),(355,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 13:33:39','10.2.26.233'),(356,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 13:34:13','10.2.26.233'),(357,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 13:49:54','::1'),(358,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 14:11:14','10.2.30.234'),(359,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 14:11:19','10.2.30.234'),(360,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 14:17:05','::1'),(361,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 14:29:15','10.2.26.233'),(362,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 15:32:08','::1'),(363,10,'crear','usuarios',10,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"admin\"}','2025-05-12 15:32:09','::1'),(364,14,'crear','usuarios',14,'{\"descripcion\":\"Inicio de sesi\\u00f3n exitoso\",\"usuario\":\"aprendiz\"}','2025-05-12 15:40:13','::1'),(365,NULL,'modificar','herramientas_no_consumibles',16,'{\"descripcion\": \"Herramienta no consumible modificada\", \"nombre\": \"llave 8\", \"cantidad_anterior\": 10, \"cantidad_nueva\": 10, \"estado_anterior\": \"\", \"estado_nuevo\": \"\"}','2025-05-12 16:12:11',NULL);
/*!40000 ALTER TABLE `auditorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `herramientas_consumibles`
--

DROP TABLE IF EXISTS `herramientas_consumibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herramientas_consumibles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` enum('recargar','medio','lleno') DEFAULT 'lleno',
  `codigo_barras` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_barras` (`codigo_barras`),
  KEY `fk_hc_usuario` (`usuario_id`),
  CONSTRAINT `fk_hc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `herramientas_consumibles`
--

LOCK TABLES `herramientas_consumibles` WRITE;
/*!40000 ALTER TABLE `herramientas_consumibles` DISABLE KEYS */;
INSERT INTO `herramientas_consumibles` VALUES (1,'tornillos',101,'lleno','3081151638','',NULL,NULL),(7,'aceite',100,'lleno','HERR_680a4840ed255','',NULL,NULL),(11,'dsad',12,'recargar','','','Taller',NULL);
/*!40000 ALTER TABLE `herramientas_consumibles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_hconsumibles_insert` AFTER INSERT ON `herramientas_consumibles` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_herramientas_consumibles_update` AFTER UPDATE ON `herramientas_consumibles` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_herramientas_consumibles_delete` AFTER DELETE ON `herramientas_consumibles` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `herramientas_no_consumibles`
--

DROP TABLE IF EXISTS `herramientas_no_consumibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `herramientas_no_consumibles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` enum('Activa','Prestada') DEFAULT 'Activa',
  `codigo_barras` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_barras` (`codigo_barras`),
  KEY `fk_hnc_usuario` (`usuario_id`),
  CONSTRAINT `fk_hnc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `herramientas_no_consumibles`
--

LOCK TABLES `herramientas_no_consumibles` WRITE;
/*!40000 ALTER TABLE `herramientas_no_consumibles` DISABLE KEYS */;
INSERT INTO `herramientas_no_consumibles` VALUES (5,'martillo',32,'Activa','4172420773','',NULL,NULL),(16,'llave 8',10,'','3245420773','',NULL,10);
/*!40000 ALTER TABLE `herramientas_no_consumibles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_hnconsumibles_insert` AFTER INSERT ON `herramientas_no_consumibles` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `*` AFTER UPDATE ON `herramientas_no_consumibles` FOR EACH ROW
BEGIN
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
            'modificar',
            'herramientas_no_consumibles',
            NEW.id,
            JSON_OBJECT(
                'descripcion', 'Herramienta no consumible modificada',
                'nombre', NEW.nombre,
                'cantidad_anterior', OLD.cantidad,
                'cantidad_nueva', NEW.cantidad,
                'estado_anterior', OLD.estado,
                'estado_nuevo', NEW.estado
            ),
            NOW(),
            NULL
        );
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_herramientas_no_consumibles_delete` AFTER DELETE ON `herramientas_no_consumibles` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aprendiz` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `herramienta_id` int(11) NOT NULL,
  `herramienta_tipo` enum('consumible','no_consumible') NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `fecha_prestamo` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_devolucion` timestamp NULL DEFAULT NULL,
  `estado` enum('prestado','devuelto','pendiente','consumida') DEFAULT 'prestado',
  PRIMARY KEY (`id`),
  KEY `fk_prestamos_aprendiz` (`id_aprendiz`),
  KEY `fk_prestamos_h_no_consumible` (`herramienta_id`),
  KEY `fk_prestamos_usuario` (`usuario_id`),
  CONSTRAINT `fk_prestamos_aprendiz` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id`),
  CONSTRAINT `fk_prestamos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
INSERT INTO `prestamos` VALUES (8,1,NULL,5,'no_consumible',2,'2025-03-27 00:57:57','2025-04-01 19:25:20','devuelto'),(9,2,NULL,1,'consumible',3,'2025-03-27 01:05:48',NULL,'prestado'),(10,1,NULL,1,'consumible',12,'2025-03-27 01:25:17',NULL,'prestado'),(11,1,NULL,4,'no_consumible',5,'2025-03-27 01:47:05','2025-03-27 02:19:43','devuelto'),(12,1,NULL,2,'consumible',20,'2025-03-27 02:24:08',NULL,'prestado'),(13,1,NULL,4,'no_consumible',12,'2025-03-27 02:25:39','2025-03-27 02:29:42','devuelto'),(14,2,NULL,7,'no_consumible',10,'2025-03-27 02:31:07','2025-03-27 21:30:16','devuelto'),(15,2,NULL,7,'no_consumible',8,'2025-03-27 02:35:05','2025-03-27 21:30:11','devuelto'),(16,2,NULL,1,'consumible',2,'2025-03-27 17:29:37',NULL,'prestado'),(17,2,NULL,1,'consumible',30,'2025-03-27 17:29:54',NULL,'prestado'),(18,2,NULL,7,'no_consumible',2,'2025-03-27 21:00:44','2025-03-27 21:09:58','devuelto'),(19,1,NULL,5,'no_consumible',20,'2025-03-27 21:31:45','2025-03-27 21:32:58','devuelto'),(20,2,NULL,5,'no_consumible',20,'2025-03-27 21:36:09','2025-03-27 21:36:36','devuelto'),(21,1,NULL,8,'no_consumible',30,'2025-03-27 21:41:30','2025-03-27 21:41:40','devuelto'),(22,2,NULL,5,'no_consumible',3,'2025-04-01 18:48:59','2025-04-01 18:55:16','devuelto'),(23,1,NULL,5,'no_consumible',10,'2025-04-01 18:56:15','2025-04-01 19:09:15','devuelto'),(24,2,NULL,5,'no_consumible',3,'2025-04-01 19:09:40','2025-04-01 19:10:07','devuelto'),(25,2,NULL,5,'no_consumible',10,'2025-04-01 19:10:35','2025-04-01 19:25:42','devuelto'),(26,1,NULL,5,'no_consumible',2,'2025-04-01 19:27:55','2025-04-01 20:26:14','devuelto'),(27,1,NULL,8,'no_consumible',23,'2025-04-01 20:25:40','2025-04-01 20:26:11','devuelto'),(28,2,NULL,8,'no_consumible',4,'2025-04-01 20:26:05','2025-04-01 20:40:01','devuelto'),(29,2,NULL,8,'no_consumible',2,'2025-04-01 20:37:24','2025-04-01 20:41:01','devuelto'),(30,1,NULL,8,'no_consumible',2,'2025-04-01 20:40:22','2025-04-01 20:41:03','devuelto'),(31,2,NULL,5,'no_consumible',2,'2025-04-07 17:55:45','2025-04-07 17:57:10','devuelto'),(32,1,NULL,5,'no_consumible',2,'2025-04-07 17:55:53','2025-04-17 16:20:11','devuelto'),(33,2,NULL,5,'no_consumible',2,'2025-04-07 18:04:54','2025-04-17 17:10:53','devuelto'),(34,2,NULL,2,'consumible',50,'2025-04-10 13:13:57',NULL,'prestado'),(35,4,NULL,5,'no_consumible',2,'2025-04-17 17:10:33','2025-04-23 15:38:07','devuelto'),(36,4,NULL,1,'consumible',60,'2025-04-17 17:12:15',NULL,'prestado'),(37,4,NULL,2,'consumible',80,'2025-04-17 18:22:33',NULL,'prestado'),(38,4,NULL,1,'consumible',80,'2025-04-17 18:24:48',NULL,'prestado'),(39,4,NULL,2,'consumible',80,'2025-04-17 18:32:18',NULL,'prestado'),(40,3,NULL,1,'consumible',2,'2025-04-23 15:27:10',NULL,'prestado'),(41,2,NULL,5,'no_consumible',5,'2025-04-23 15:35:32','2025-04-23 16:03:17','devuelto'),(42,2,NULL,5,'no_consumible',2,'2025-04-23 15:41:04','2025-04-23 19:10:09','devuelto'),(43,3,NULL,5,'no_consumible',3,'2025-04-23 15:55:44','2025-04-23 16:48:42','devuelto'),(44,3,NULL,5,'no_consumible',3,'2025-04-23 15:55:57','2025-04-23 16:03:14','devuelto'),(45,2,NULL,5,'no_consumible',2,'2025-04-23 15:56:58','2025-04-23 16:03:12','devuelto'),(51,3,NULL,5,'no_consumible',4,'2025-04-23 23:01:07','2025-04-24 17:01:51','devuelto'),(52,3,NULL,5,'no_consumible',5,'2025-04-24 13:27:41','2025-04-24 17:01:55','devuelto'),(53,3,NULL,1,'consumible',50,'2025-04-24 13:29:13',NULL,'prestado'),(60,3,NULL,5,'no_consumible',5,'2025-04-24 14:02:54','2025-04-24 20:01:25','devuelto'),(61,2,10,1,'consumible',100,'2025-04-24 15:54:01',NULL,'prestado'),(62,2,10,1,'consumible',233,'2025-04-24 16:19:31',NULL,'prestado'),(63,1,NULL,5,'no_consumible',3,'2025-04-24 17:48:06','2025-04-26 15:24:26','devuelto'),(64,3,10,5,'no_consumible',2,'2025-04-26 15:23:50','2025-04-26 15:24:22','devuelto'),(65,1,14,5,'no_consumible',5,'2025-04-26 16:25:20','2025-04-26 17:09:16','devuelto'),(66,1,10,5,'no_consumible',5,'2025-04-26 17:10:35','2025-04-26 17:11:16','devuelto'),(67,3,10,5,'no_consumible',2,'2025-04-30 16:09:19','2025-04-30 16:09:44','devuelto');
/*!40000 ALTER TABLE `prestamos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `check_herramienta_consumible` BEFORE INSERT ON `prestamos` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_prestamos_insert` AFTER INSERT ON `prestamos` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_prestamos_update` AFTER UPDATE ON `prestamos` FOR EACH ROW
BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aprendiz` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `fecha_reporte` datetime NOT NULL,
  `resuelto` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_aprendiz` (`id_aprendiz`),
  CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_aprendiz`) REFERENCES `aprendices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportes`
--

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
INSERT INTO `reportes` VALUES (2,1,'Herramientas pendientes: martillo (cantidad: 20)','2025-03-27 11:31:57',1),(3,2,'Herramientas pendientes: martillo (cantidad: 20)','2025-03-27 11:36:23',1),(4,1,'Herramientas pendientes: martillo (cantidad: 10) | Total préstamos pendientes: 1','2025-04-01 09:09:01',0),(5,2,'Herramientas pendientes: martillo (cantidad: 3) | Total préstamos pendientes: 1','2025-04-01 09:09:58',1),(6,1,'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1','2025-04-07 07:56:51',0),(7,2,'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1','2025-04-07 07:56:51',0),(8,4,'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1','2025-04-17 12:10:57',1),(9,4,'Herramientas pendientes: martillo (cantidad: 2) | Total préstamos pendientes: 1','2025-04-17 13:35:05',0);
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('administrador','usuario') NOT NULL DEFAULT 'usuario',
  `telefono` varchar(20) DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `expiracion_token` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (10,'admin','$2y$10$4K7HJUlvvHrbQGTll5bmbuHsKpyIoxxyzjlpFqY8GlLmnhACQTRPy','administrador','+573006547363','f32a5c5fdb9c6910f79e135b544aff614c22b10d468ec3d1e9e43b2a3ebceee2','2025-06-11 17:32:09','2025-04-24 14:25:07'),(14,'aprendiz','$2y$10$XzQDN6AkvD8UIRUOP.Qfcuhe2HGVAaXYZnI6DAm/U7tIKfS.2sFny','usuario','+57300546332','d0169c820129af393d756a93885a7b33f1337d864487377586f174d7e7e5ec93','2025-06-11 17:40:13','2025-04-26 16:23:53');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_usuarios_delete` BEFORE DELETE ON `usuarios` FOR EACH ROW
BEGIN
    DECLARE usuario_valido INT DEFAULT NULL;

    -- Usar @current_user_id si está definido y existe en usuarios
    IF @current_user_id IS NOT NULL THEN
        SELECT id INTO usuario_valido
        FROM usuarios
        WHERE id = @current_user_id
        LIMIT 1;
    END IF;

    -- Deshabilitar temporalmente la verificación de claves foráneas
    SET FOREIGN_KEY_CHECKS = 0;

    -- Actualizar registros relacionados en auditorias
    UPDATE auditorias SET usuario_id = NULL WHERE usuario_id = OLD.id;

    -- Insertar registro de auditoría
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
        'usuarios',
        OLD.id,
        JSON_OBJECT(
            'descripcion', 'Eliminación de usuario',
            'usuario', OLD.usuario,
            'rol', OLD.rol
        ),
        NOW(),
        NULL
    );

    -- Restaurar la verificación de claves foráneas
    SET FOREIGN_KEY_CHECKS = 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-13  7:20:29
