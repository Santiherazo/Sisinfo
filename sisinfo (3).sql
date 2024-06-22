-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-06-2024 a las 14:11:08
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
-- Base de datos: `sisinfo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `assessor` varchar(255) NOT NULL,
  `titleProject` decimal(3,1) NOT NULL,
  `feedProject` text DEFAULT NULL,
  `introduction` decimal(3,1) NOT NULL,
  `feedIntroduction` text DEFAULT NULL,
  `problemStatement` decimal(3,1) NOT NULL,
  `FeedStatement` text DEFAULT NULL,
  `justify` decimal(3,1) NOT NULL,
  `feedJustify` text DEFAULT NULL,
  `targets` decimal(3,1) NOT NULL,
  `feedTargets` text DEFAULT NULL,
  `theorical` decimal(3,1) NOT NULL,
  `feedTheorical` text DEFAULT NULL,
  `methodology` decimal(3,1) NOT NULL,
  `feedMethodology` text DEFAULT NULL,
  `mainResults` decimal(3,1) NOT NULL,
  `feedMainresults` text DEFAULT NULL,
  `support` decimal(3,1) NOT NULL,
  `feedSupport` text DEFAULT NULL,
  `rating` decimal(4,1) NOT NULL,
  `generalComments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `investigadores` text DEFAULT NULL,
  `docentes` varchar(255) NOT NULL,
  `linea` enum('Ingeniería del Software','Gestión de la Seguridad Informática','Redes y Telemática','Ingeniería del conocimiento','Robótica') DEFAULT NULL,
  `evaluador` varchar(255) NOT NULL,
  `fase` enum('Propuesta','Desarrollo','Aplicacion') DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `palabras_clave` varchar(255) DEFAULT NULL,
  `calificacion` decimal(3,2) DEFAULT NULL,
  `calificado` tinyint(1) NOT NULL,
  `timer` int(11) NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `investigadores`, `docentes`, `linea`, `evaluador`, `fase`, `titulo`, `descripcion`, `fecha_inicio`, `imagen`, `palabras_clave`, `calificacion`, `calificado`, `timer`, `hora`, `created_at`) VALUES
(1, '65', 'Carol Tatiana Bareño León, Jhonis Ríos Múnera', 'Ingeniería del Software', '1,2', 'Desarrollo', 'Serena: Aplicativo web para el apoyo en el seguimiento y caracterización de estudiantes con capacidades diversas.', NULL, NULL, NULL, NULL, NULL, 0, 20, '08:00:00', '2024-06-07 22:28:56'),
(2, '66,22,13,67', 'Edwin Durán Blandón, Cipriano López Vides', 'Ingeniería del Software', '1,2', 'Desarrollo', 'Fire Controller: Sistema Inteligente para el control en tiempo real de incendios forestales', NULL, NULL, NULL, NULL, NULL, 0, 20, '08:20:00', '2024-06-07 22:28:56'),
(3, '68,17,25', 'Edwin Durán Blandón', 'Ingeniería del Software', '1,2', 'Propuesta', 'DisMathPaz', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(4, '69,70,71,72', 'Edwin Durán Blandón, Cipriano Lopez Videz', 'Ingeniería del Software', '1,2', 'Desarrollo', 'CHATBOT \"BOTI WMS\"', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(5, '69,70,71,72', 'Edwin Durán Blandón, Cipriano López Vides', 'Ingeniería del Software', '1,2', 'Desarrollo', 'Chatbot  como apoyo al aprendizaje del área de cálculo  para estudiantes de ingeniería de UNIPAZ', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(6, '73', 'Hermes Alejandro Acevedo Castellanos', 'Ingeniería del Software', '1,2', 'Propuesta', 'Aplicación web interactiva utilizando Angular para el aprendizaje de inglés en niños de preescolar.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(7, '74,27', 'Hermes Alejandro Acevedo Castellanos', 'Ingeniería del Software', '1,2', 'Propuesta', 'Aplicación de gestión de tareas utilizando herramientas colaborativas en un entorno de desarrollo ágil.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(8, '75,76,20', 'Javier Enrique Berrio Polanco, Juan Andres Santos', 'Ingeniería del Software', '1,2', 'Propuesta', 'Desarrollo de Software de registro y gestión de la población de bovinos para productores de ganado.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(9, '77,78,79', 'Javier Enrique Berrio Polanco', 'Ingeniería del Software', '1,2', 'Propuesta', 'Desarrollo de una aplicación web para el apartado y venta de productos de las cafeterías del Instituto Universitario de la Paz.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(10, '7,29,80,23', 'Javier Enrique Berrio Polanco', 'Ingeniería del Software', '1,2', 'Propuesta', 'Desarrollo de una plataforma web gestionable para brindar información descriptiva de juegos en parques sensoriales.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(11, '0', 'Cipriano López Vides, Edwin Duran Blandón', '', '3', 'Propuesta', 'Fire Controller: Sistema Inteligente para el control en tiempo real de incendios forestales', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(12, '0', 'Cipriano López Vides, Edwin Duran Blandón', '', '3', 'Propuesta', 'MathBot: Optimización del Aprendizaje Matemático en UNIPAZ', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(13, '0', 'Cipriano López Vides', '', '3', 'Propuesta', 'Children\'s learning - Plataforma enfocada el aprendizaje infantil', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(14, '0', 'Cipriano López Vides', '', '3', 'Propuesta', '(ZAFIRO). ASISTENTE VIRTUAL CON IMPLEMENTACIÓN DE UNA IA CON CONOCIMIENTO EN MULTITAREAS.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(15, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Repositorio para métodos de estudios', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(16, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Diseño de una aplicación web para la gestión y seguimiento del servicio técnico', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(17, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Implementación de un sistema de control de asistencia con tecnología QR y desarrollo de sistema web y aplicativo móvil - KronusTrack', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(18, '0', 'Cipriano López Vides', '', '', 'Aplicacion', 'Sistema de reservas de salas, salones y equipos del Instituto Universitario de la Paz Unipaz. BIBLIOPAZ', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(19, '0', 'Cipriano López Vides, Edwin Durán Blandón', '', '', 'Propuesta', 'Juego educacional sobre problemas de matemáticas 3 abarcando temas específicos. (playmath)', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(20, '0', 'Rogers Smith Carranza Guzman', '', '', 'Desarrollo', 'Rhesus me', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(21, '0', 'Rogers Smith Carranza Guzman', '', '', 'Propuesta', 'RURALPROTECT', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(22, '0', 'Rogers Smith Carranza Guzman', '', '', 'Propuesta', 'Laberinto X', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(23, '0', 'Rogers Smith Carranza Guzman', '', '', 'Propuesta', 'Diseño de un aplicativo web para el diagnóstico temprano de enfermedades en equinos', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(24, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Desarrollo de una Plataforma de Apoyo a la Práctica Legal: Implementación de una Base de Datos y un Asistente Virtual para Abogados', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(25, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Aplicativo Móvil y Web para la Toma de Pedidos en Tiempo Real en Restaurantes', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(26, '0', 'Cipriano López Vides, Karen Jhoanna Salom Mantilla', '', '', 'Desarrollo', 'RUBRIC DEV ONLINE Desarrollo de herramienta informática de evaluación y seguimiento para las propuestas de proyectos inscritos y presentados en la “rueda de proyectos” del programa de ingeniería informática del instituto universitario de la paz – UNIPAZ', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(27, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'ALERTAS TEMPRANAS', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(28, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Juego de Adivinar Capitales de Colombia', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(29, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Desarrollo De Aplicación Web Con Finalidad De Centralizar Todos Proyectos De Grado Del Instituto Universitario De La Paz', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56'),
(30, '0', 'Cipriano López Vides', '', '', 'Propuesta', 'Sistema de reservas de salas, salones y equipos del Instituto Universitario de la Paz UNIPAZ.', NULL, NULL, NULL, NULL, NULL, 0, 20, '00:00:00', '2024-06-07 22:28:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `documento_identidad` int(10) DEFAULT NULL,
  `carnet` int(10) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Estudiante','Evaluador','Administrador','Coordinador') DEFAULT NULL,
  `institucion` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `estado_provincia` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil_url` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `correo_electronico`, `documento_identidad`, `carnet`, `contrasena`, `rol`, `institucion`, `direccion`, `ciudad`, `estado_provincia`, `pais`, `codigo_postal`, `telefono`, `foto_perfil_url`, `fecha_nacimiento`, `genero`, `fecha_registro`) VALUES
(1, 'Jeisser Jose Rodríguez Cervantes', 'jeisser.rodriguez@unipaz.edu.co', 72100175, NULL, 'e602bd2cb38083c481436ce0efbaf1d798d97402', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(2, 'Jeimy Mauricio Tolosa Bermúdez', 'mauricio.tolosa@unipaz.edu.co', 91443134, NULL, 'a268c013dd34a7ec73d8bce14fa323d161b956bc', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(3, 'Javier Enrique Berrio Polanco', 'javier.berrio@unipaz.edu.co', 13741097, NULL, '508c1b7a4569cb3c97b956c5e22dee98bd3dc362', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(4, 'Rogers Smith Carranza Guzmán', 'rogers.carranza@unipaz.edu.co', 91528289, NULL, '76f574b7e41b749d0b9f26f3640980959e0b26d3', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(5, 'Edwin Durán Blandón', 'edwin.duran@unipaz.edu.co', 91494836, NULL, '9e2627523b1eb334d5d381317615771a26109de5', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(6, 'Jhonis Ríos Munera', 'jhoni.rios@unipaz.edu.co', 15451785, NULL, '618c59ac0dc36ab00f62ffaf1086ce7f79fafec8', 'Evaluador', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(7, 'Adrian Ferney Mendoza Garcia', 'adrianf.mendizag@unipaz.edu.co', 1096800972, NULL, '954851b15486e94a00a4301a7b13ecaeba409b0a', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(8, 'Adriana Castillo Grandas', 'adriana.castillogr@unipaz.edu.co', 1097990798, NULL, '8ee6f8d10f71f11ff63b370b7c4dc37e2f4ab589', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(9, 'Álvaro Álvarez', 'alvaro.alvarez@unipaz.edu.co', 1002495355, NULL, '81eeb1d9105ee195f537ad1eeed4a5a1e7a71aa3', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(10, 'Amilkar Antonio Castro Mejia', 'amilkara.castrom@unipaz.edu.co', 1051735678, NULL, '969eaae83d9790977a623d254a82d36f34b941cb', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(11, 'Andres Camilo Pineda Gamarra', 'andres.pinedaga@unipaz.edu.co', 1065862007, NULL, '872f39e6348541a5ae3734b89dca68e8e76d932f', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(12, 'Andres Felipe Sanabria Galvis', 'andres.sanabria@unipaz.edu.co', 1097093368, NULL, '1097093368', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(13, 'Andres Ferney Garcia', 'andres.garciaga@unipaz.edu.co', 1005564075, NULL, '1005564075', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(14, 'Brayan Trespalacios', 'brayan.trespalacios@unipaz.edu.co', 1007618321, NULL, '1007618321', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(15, 'Carlos Andres Martinez Alvear', 'carlos.martinezal@unipaz.edu.co', 1091132605, NULL, '1091132605', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(16, 'Carlos Eduardo Garcia Jimenez', 'carlos.garciaji@unipaz.edu.co', 1005185860, NULL, '1005185860', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(17, 'Carlos Jose Rangel Rincon', 'carlos.rangelri@unipaz.edu.co', 1096197352, NULL, '1096197352', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(18, 'Chistopher Henry Mcnish Castrillon', 'chistopher.mcnishca@unipaz.edu.co', 1010840424, NULL, '1010840424', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(19, 'Cristhian Antonio Hernandez Pineda', 'cristhian.hernandezp@unipaz.edu.co', 1096231044, NULL, '1096231044', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(20, 'Cristhian Camilo Torres Rueda', 'cristhianc.torresr@unipaz.edu.co', 1096801813, NULL, '1096801813', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(21, 'Cristian David Meneses Rueda', 'cristian.menesesru@unipaz.edu.co', 1097185717, NULL, '1097185717', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(22, 'Daniel Santiago Angel Gonzalez Ubaque', 'daniel.gonzalezubaque@unipaz.edu.co', 1096801379, NULL, '1096801379', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(23, 'Daniel Turizo', 'danielj.turizoa@unipaz.edu.co', 0, NULL, '0', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(24, 'Darwin Garcia Andrade', 'darwin.garciaan@unipaz.edu.co', 1002419884, NULL, '1002419884', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(25, 'Enoc Diaz Macias', 'enoc.diaz@unipaz.edu.co', 1005220507, NULL, '1005220507', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(26, 'Erick Santiago Vega Morales', 'erick.vegamo@unipaz.edu.co', 1096801530, NULL, '1096801530', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(27, 'Fabriany Pico Lopez', 'fabriany.pico@unipaz.edu.co', 1005595110, NULL, '1005595110', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(28, 'Fredy Andres Quintanilla Perez', 'fredy.quintanillape@unipaz.edu.co', 1005185815, NULL, '1005185815', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(29, 'Hadik Andres Chavez Villafane', 'hadik.chavezv@unipaz.edu.co', 1005239396, NULL, '1005239396', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(30, 'Harold Martinez Martinez', 'harold.martinezma@unipaz.edu.co', 1097182560, NULL, '1097182560', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(31, 'Jamer De Jesus Carcamo Pianeta', 'jamer.caracamo@unipaz.edu.co', 1005184229, NULL, '1005184229', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(32, 'Jeferson Alexander Hernandez Becerra', 'jeferson.hernandezb@unipaz.edu.co', 1065739736, NULL, '1065739736', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(33, 'Jeferson Carcamo', 'jeferson.carcamo@unipaz.edu.co', 1007058010, NULL, '1007058010', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(34, 'Jeferson Mauricio Rangel Rincon', 'jeferson.rangelri@unipaz.edu.co', 1005191044, NULL, '1005191044', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(35, 'Jeferson Steven Salazar Mendez', 'jeferson.salazarm@unipaz.edu.co', 1097091632, NULL, '1097091632', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(36, 'Jhon Mario Amaya Hernandez', 'jhon.amayah@unipaz.edu.co', 1048554420, NULL, '1048554420', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(37, 'Jhon Mario Munera Rios', 'jhonis.munera@unipaz.edu.co', 15451785, NULL, '15451785', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(38, 'Jhonatan Jose Lopez Oviedo', 'jhonatan.lopez@unipaz.edu.co', 1007211967, NULL, '1007211967', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(39, 'Jhonatan Mauricio Sanchez Vargas', 'jhonatan.sanchezva@unipaz.edu.co', 1007054018, NULL, '1007054018', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(40, 'Jose Julian Perez Guiza', 'jose.perezgui@unipaz.edu.co', 1005027308, NULL, '1005027308', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(41, 'Jose Luis Hernandez Perez', 'jose.hernandezpe@unipaz.edu.co', 1005582851, NULL, '1005582851', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(42, 'Julieth Paola Vasquez Perez', 'julieth.vasquezpe@unipaz.edu.co', 1096882430, NULL, '1096882430', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(43, 'Kathy Lorena Contreras Villamizar', 'kathy.contrerasvi@unipaz.edu.co', 1096797547, NULL, '1096797547', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(44, 'Laura Vanessa Paredes Gomez', 'laura.paredesgo@unipaz.edu.co', 1096785595, NULL, '1096785595', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(45, 'Linda Janeth Galvis Jaimes', 'linda.galvisja@unipaz.edu.co', 1002483664, NULL, '1002483664', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(46, 'Luis Alejandro Carrillo', 'luis.carrillo@unipaz.edu.co', 1006822055, NULL, '1006822055', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(47, 'Luis Angel Reyes Suarez', 'luis.reyessua@unipaz.edu.co', 1097053740, NULL, '1097053740', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(48, 'Luis Carlos Diaz', 'luis.diazdi@unipaz.edu.co', 1005296600, NULL, '1005296600', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(49, 'Luis Enrique Melendez', 'luis.melendezme@unipaz.edu.co', 1005098796, NULL, '1005098796', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(50, 'Maria Andrea Garcia Diaz', 'maria.garciadi@unipaz.edu.co', 1002483781, NULL, '1002483781', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(51, 'Marlon De Jesus Jaimes Gomez', 'marlon.jaimesgo@unipaz.edu.co', 1002494083, NULL, '1002494083', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(52, 'Melanie Alexandra Gaviria Sandoval', 'melanie.gaviria@unipaz.edu.co', 1096801273, NULL, '1096801273', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(53, 'Miguel Angel Pena Rueda', 'miguel.penarue@unipaz.edu.co', 1096801934, NULL, '1096801934', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(54, 'Milena Marcela Gamarra Montes', 'milena.gamarramo@unipaz.edu.co', 1096801398, NULL, '1096801398', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(55, 'Nestor Fabio Hernandez', 'nestor.hernandezhe@unipaz.edu.co', 1096786677, NULL, '1096786677', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(56, 'Paola Alejandra Vargas Ruiz', 'paola.vargasru@unipaz.edu.co', 1002483781, NULL, '1002483781', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(57, 'Rene Alexander Galeano Celis', 'rene.galeanoce@unipaz.edu.co', 1005536450, NULL, '1005536450', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(58, 'Samir Alexis Becerra Hernandez', 'samir.becerrahe@unipaz.edu.co', 1096800965, NULL, '1096800965', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(59, 'Sebastian David Jerez Martinez', 'sebastian.jerezma@unipaz.edu.co', 1007247337, NULL, '1007247337', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(60, 'Sebastian Javier Nuñez Castro', 'sebastian.nunezca@unipaz.edu.co', 1096801284, NULL, '1096801284', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(61, 'Sergio Andres Rodriguez Miranda', 'sergio.rodriguezmi@unipaz.edu.co', 1096801997, NULL, '1096801997', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(62, 'Valentina Castilla Morales', 'valentina.castillamo@unipaz.edu.co', 1007252431, NULL, '1007252431', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Femenino', '2024-06-07 17:21:06'),
(63, 'Wilder Manjarrez', 'wilder.manjarrez@unipaz.edu.co', 1096785596, NULL, '1096785596', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(64, 'William Santiago Pardo Olaya', 'william.pardoo@unipaz.edu.co', 1007198304, NULL, '1007198304', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, 'Masculino', '2024-06-07 17:21:06'),
(65, 'Juan José Gómez Duarte', 'juan.gomezd@unipaz.edu.co', 1005548923, NULL, '07fd3e6e80558645d43bb6c6aeedcec1b93b51d2', NULL, 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:07:38'),
(66, 'Nicolás Adrián Aguilar Rúa', 'nicolas.aguilarru@unipaz.edu.co', 1193287312, NULL, '0041d45a6dbfb142590364e7e84ed5a0207ebed1', 'Estudiante', 'Unipaz', NULL, 'Barrancabermeja', 'Santander', 'Colombia', NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:14:29'),
(67, 'Juan Sebastián Giraldo', 'juans.rincong@unipaz.edu.co', 0, NULL, '4e079d0555e5a2b460969c789d3ad968a795921f', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:19:21'),
(68, 'Johan Manuel Gonzalez Brionis', 'johan.gonzalezbr@unipaz.edu.co', 1096801511, NULL, '4f9f090d2840f534024d94ed9f1a7675df7e7faf', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:24:55'),
(69, 'Margit Dayanna Barrera Gutiérrez', 'margit.barreragu@unipaz.edu.co', 1014189353, NULL, '7ada781dbc8d564cd0cf4f99bf30ee5c7776baa5', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:33:23'),
(70, 'Said Alexander Pacheco Moncada', 'said.pacheco@unipaz.edu.co', 1092528951, NULL, '1bd98e2f2b4cbcdb10831c507cee342551785769', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:35:04'),
(71, 'Michel Daniela Bayona Díaz', 'michel.bayonadi@unipaz.edu.co', 1096802414, NULL, 'd890960582fcd73ea4d97f6397eb8113b8e67a9d', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:37:18'),
(72, 'Wilfran Leonardo Betancourt Ardila', 'wilfran.betancourta@unipaz.edu.co', 1097184429, NULL, '7d018c0ea5c1abc5583b28725a99cb3e73c880b2', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:38:36'),
(73, 'Juan David Torres Bolivar', 'jhonathan.sanchez@unipaz.edu.co', 1096800932, NULL, 'b642b86ee2cf69394cfa293bc99ad38bcb5e6467', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:44:07'),
(74, 'Karol Lisseth Barreto Benjumea', 'karol.barreto@unipaz.edu.co', 1005240596, NULL, '32841abe2feecece983a21e15fb3cf2b388dcdda', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:46:45'),
(75, 'Javier Enrique Molina Lizaraso', 'javerie.molinal@unipaz.edu.co', 1193572974, NULL, '19e3fbff378836226f2d5cf1dadf607c7170462d', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:51:34'),
(76, 'Yon Alex Jaraba Leon', 'yona.jarabal@unipaz.edu.co', 1096186948, NULL, 'eafe0a03b714ca545c7a061f62eebd2cd4567ac3', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:54:26'),
(77, 'Jose Andres Ayala Ochoa', 'josea.ayalao@unipaz.edu.co', 1050542002, NULL, '43c929a741d4128c22883c83f4c2776aa90fbf43', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 02:58:03'),
(78, 'Jose Carlos Montero Patiño', 'josec.monterop@unipaz.edu.co', 1015998478, NULL, '2fea01c6cc67f7a557035e758e158560fc91440e', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 03:00:31'),
(79, 'Kevin Sair Rodriguez Quintero', 'kevins.rodriguezq@unipaz.edu.co', 1096801380, NULL, 'cf2ef2a65eba8d0761529862854969976ee6cd33', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 03:02:24'),
(80, 'Miguel Angel Cifuentes Rodriguez', 'miguel.cifuentesr@unipaz.edu.co', 1012325001, NULL, 'e20be576dc9ea62f7364277d50d64b2d6fd39c04', 'Estudiante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-08 03:09:01');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`idProject`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`idProject`) REFERENCES `proyectos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
