-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2024 a las 14:54:11
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

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `idProject`, `assessor`, `titleProject`, `feedProject`, `introduction`, `feedIntroduction`, `problemStatement`, `FeedStatement`, `justify`, `feedJustify`, `targets`, `feedTargets`, `theorical`, `feedTheorical`, `methodology`, `feedMethodology`, `mainResults`, `feedMainresults`, `support`, `feedSupport`, `rating`, `generalComments`) VALUES
(1, 1, '4.50', 1.0, '', 0.0, '', 1.0, '', 1.0, NULL, 0.0, NULL, 0.0, NULL, 1.0, NULL, 1.0, NULL, 1.0, '1.0', 7.0, ''),
(2, 1, '4.50', 1.0, '', 0.0, '', 1.0, '', 1.0, NULL, 0.0, NULL, 0.0, NULL, 1.0, NULL, 1.0, NULL, 1.0, '1.0', 7.0, ''),
(3, 11, 'Dr. Ana Gómez', 1.0, '', 0.0, '', 1.0, '', 1.0, NULL, 0.0, NULL, 0.0, NULL, 1.0, NULL, 1.0, NULL, 1.0, '1.0', 7.0, ''),
(4, 11, 'Dr. Ana Gómez', 1.0, '', 0.0, '', 1.0, '', 1.0, NULL, 0.0, NULL, 0.0, NULL, 1.0, NULL, 1.0, NULL, 1.0, '1.0', 7.0, ''),
(15, 12, 'Dr. Ricardo Díaz', 5.0, '', 0.0, '', 2.0, '', 1.0, NULL, 0.0, NULL, 0.0, NULL, 1.0, NULL, 1.0, NULL, 1.0, '1.0', 12.0, ''),
(52, 11, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(53, 11, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(54, 11, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(55, 12, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(56, 12, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(57, 11, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(58, 12, 'Santiago Herazo Pérez', 5.0, '5', 0.0, '', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 5.0, '5', 40.0, '5'),
(59, 12, 'Santiago Herazo Pérez', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 45.0, '5'),
(60, 11, 'Santiago Herazo Pérez', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 45.0, '5'),
(61, 43, 'María Hernández', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 45.0, '5'),
(62, 43, 'María Hernández', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 5.0, '', 45.0, '5');

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
  `fecha_fin` date DEFAULT NULL,
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

INSERT INTO `proyectos` (`id`, `investigadores`, `docentes`, `linea`, `evaluador`, `fase`, `titulo`, `descripcion`, `fecha_inicio`, `fecha_fin`, `imagen`, `palabras_clave`, `calificacion`, `calificado`, `timer`, `hora`, `created_at`) VALUES
(11, 'Juan Pérez, María López, Sergio Cornejo', 'Dr. Luis García', 'Ingeniería del conocimiento', 'Santiago Herazo Pérez', 'Propuesta', 'Impacto del Cambio Climático', 'Estudio sobre el impacto del cambio climático en la biodiversidad.', '2024-01-01', '2024-12-31', 'imagen1.jpg', 'cambio climático, biodiversidad, medio ambiente', 9.99, 1, 20, '13:00:00', '2024-05-26 15:00:00'),
(12, 'Carlos Sánchez, Laura Martínez', 'Dra. Elena Torres', 'Gestión de la Seguridad Informática', 'Santiago Herazo Pérez', 'Propuesta', 'Desarrollo de Biofertilizantes', 'Investigación sobre biofertilizantes a partir de residuos orgánicos.', '2023-02-15', '2024-06-30', 'imagen2.jpg', 'biofertilizantes, residuos orgánicos, agricultura sostenible', 9.99, 1, 20, '10:00:00', '2024-05-26 15:10:00'),
(31, 'Juan Pérez, María González', 'María González', 'Ingeniería del Software', 'Juan Pérez', 'Propuesta', 'Desarrollo de un sistema de gestión de proyectos', 'Desarrollo de un sistema de gestión de proyectos para la empresa XYZ.', '2022-01-01', '2022-06-30', 'imagen1.jpg', 'gestión de proyectos, sistema de gestión', 0.00, 0, 0, '08:00:00', '2022-01-01 13:00:00'),
(32, 'Pedro Rodríguez, Ana Martínez, Luis García', 'Ana Martínez, Carlos López', '', 'Pedro Rodríguez, Luis García', 'Desarrollo', 'Seguridad en la nube y Redes de alta velocidad', 'Desarrollo de un sistema de seguridad en la nube y redes de alta velocidad para las empresas ABC y DEF.', '2022-02-01', '2022-09-30', 'imagen2.jpg', 'seguridad en la nube, sistema de seguridad, redes de alta velocidad, sistema de redes', 0.00, 0, 0, '09:00:00', '2022-02-01 14:00:00'),
(33, 'María Hernández, Juan Carlos Sánchez, Jorge Martínez, Ana Isabel González', 'Juan Carlos Sánchez, Ana Isabel González', '', 'María Hernández, Jorge Martínez', '', 'Análisis de datos y Desarrollo de un robot', 'Análisis de datos para la empresa GHI y desarrollo de un robot para la empresa JKL.', '2022-04-01', '2022-11-30', 'imagen3.jpg', 'análisis de datos, sistema de análisis, robot, sistema de robot', 0.00, 0, 0, '11:00:00', '2022-04-01 16:00:00'),
(34, 'Eva Pérez, Carlos López', 'Carlos López', 'Redes y Telemática', 'Eva Pérez', 'Aplicacion', 'Implementación de redes de alta velocidad', 'Implementación de redes de alta velocidad para la empresa MNO.', '2022-05-01', '2022-12-31', 'imagen4.jpg', 'redes de alta velocidad, implementación de redes', 0.00, 0, 0, '12:00:00', '2022-05-01 17:00:00'),
(35, 'Jorge Martínez, Ana Isabel González', 'Ana Isabel González', 'Ingeniería del Software', 'Jorge Martínez', 'Desarrollo', 'Desarrollo de una aplicación móvil', 'Desarrollo de una aplicación móvil para la empresa PQR.', '2022-06-01', '2023-01-31', 'imagen5.jpg', 'aplicación móvil, desarrollo de software', 0.00, 0, 0, '13:00:00', '2022-06-01 18:00:00'),
(36, 'María González, Juan Carlos Sánchez', 'Juan Carlos Sánchez', 'Gestión de la Seguridad Informática', 'María González', 'Aplicacion', 'Seguridad informática en entornos corporativos', 'Implementación de medidas de seguridad informática para la empresa STU.', '2022-07-01', '2023-02-28', 'imagen6.jpg', 'seguridad informática, entornos corporativos', 0.00, 0, 0, '14:00:00', '2022-07-01 19:00:00'),
(37, 'Pedro Rodríguez, Luis García, Ana Martínez', 'Luis García, Ana Martínez', 'Redes y Telemática', 'Pedro Rodríguez, Ana Martínez', 'Propuesta', 'Diseño de una red de comunicaciones', 'Diseño de una red de comunicaciones para la empresa DEF.', '2022-08-01', '2023-03-31', 'imagen7.jpg', 'redes de comunicaciones, diseño de redes', 0.00, 0, 0, '15:00:00', '2022-08-01 20:00:00'),
(38, 'Juan Pérez, María Hernández, Jorge Martínez', 'María Hernández, Jorge Martínez', '', 'Juan Pérez, María Hernández', 'Desarrollo', 'Inteligencia artificial aplicada a la robótica', 'Desarrollo de sistemas de inteligencia artificial para robots en la empresa GHI.', '2022-09-01', '2023-04-30', 'imagen8.jpg', 'inteligencia artificial, robótica, sistemas inteligentes', 0.00, 0, 0, '16:00:00', '2022-09-01 21:00:00'),
(39, 'Ana Martínez, Luis García', 'Luis García', 'Ingeniería del Software', 'Ana Martínez', 'Aplicacion', 'Desarrollo de un sistema de gestión educativa', 'Desarrollo de un sistema de gestión educativa para instituciones educativas.', '2022-10-01', '2023-05-31', 'imagen9.jpg', 'gestión educativa, sistema educativo, desarrollo de software', 0.00, 0, 0, '17:00:00', '2022-10-01 22:00:00'),
(40, 'Carlos López, Eva Pérez, Juan Carlos Sánchez', 'Eva Pérez, Juan Carlos Sánchez', '', 'Carlos López, Eva Pérez', 'Propuesta', 'Seguridad en redes de comunicaciones', 'Implementación de medidas de seguridad en redes de comunicaciones para la empresa XYZ.', '2022-11-01', '2023-06-30', 'imagen10.jpg', 'seguridad en redes, comunicaciones seguras, medidas de seguridad', 0.00, 0, 0, '18:00:00', '2022-11-01 23:00:00'),
(41, 'Juan Pérez, María González', 'María González', 'Ingeniería del Software', 'Juan Pérez', 'Desarrollo', 'Desarrollo de un sistema de gestión de recursos', 'Desarrollo de un sistema de gestión de recursos para la empresa ABC.', '2022-12-01', '2023-07-31', 'imagen11.jpg', 'gestión de recursos, sistema de gestión', 0.00, 0, 0, '19:00:00', '2022-12-02 00:00:00'),
(42, 'Pedro Rodríguez, Ana Martínez', 'Ana Martínez', 'Gestión de la Seguridad Informática', 'Pedro Rodríguez', 'Aplicacion', 'Implementación de medidas de seguridad en la nube', 'Implementación de medidas de seguridad en la nube para la empresa DEF.', '2023-01-01', '2023-08-31', 'imagen12.jpg', 'seguridad en la nube, medidas de seguridad', 0.00, 0, 0, '20:00:00', '2023-01-02 01:00:00'),
(43, 'María Hernández, Jorge Martínez', 'Jorge Martínez', 'Ingeniería del conocimiento', 'María Hernández', 'Propuesta', 'Desarrollo de un sistema de análisis de datos', 'Desarrollo de un sistema de análisis de datos para la empresa GHI.', '2023-02-01', '2023-09-30', 'imagen13.jpg', 'análisis de datos, sistema de análisis', 9.99, 1, 6, '21:00:00', '2023-02-02 02:00:00'),
(44, 'Ana Martínez, Luis García, Carlos López', 'Luis García, Carlos López', 'Redes y Telemática', 'Ana Martínez, Carlos López', 'Desarrollo', 'Diseño e implementación de redes de alta velocidad', 'Diseño e implementación de redes de alta velocidad para la empresa JKL.', '2023-03-01', '2023-10-31', 'imagen14.jpg', 'redes de alta velocidad, diseño de redes, implementación de redes', 0.00, 0, 0, '22:00:00', '2023-03-02 03:00:00'),
(45, 'Juan Pérez, María González, Jorge Martínez', 'María González, Jorge Martínez', '', 'Juan Pérez, Jorge Martínez', 'Aplicacion', 'Desarrollo de un sistema de gestión de proyectos con robótica', 'Desarrollo de un sistema de gestión de proyectos con aplicaciones de robótica para la empresa MNO.', '2023-04-01', '2023-11-30', 'imagen15.jpg', 'gestión de proyectos, robótica, sistema de gestión', 0.00, 0, 0, '23:00:00', '2023-04-02 04:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `documento_identidad` int(10) NOT NULL,
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

INSERT INTO `usuarios` (`id`, `nombre_completo`, `nombre_usuario`, `correo_electronico`, `documento_identidad`, `contrasena`, `rol`, `institucion`, `direccion`, `ciudad`, `estado_provincia`, `pais`, `codigo_postal`, `telefono`, `foto_perfil_url`, `fecha_nacimiento`, `genero`, `fecha_registro`) VALUES
(1, NULL, 'admin', 'admin@admin.com', 0, '12345678', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-15 14:33:45'),
(2, 'Santiago Herazo Pérez', 'santiagoherazo300007', 'herazopsantiago@gmail.com', 1002199330, '7c222fb2927d828af22f592134e8932480637c0d', 'Evaluador', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-16 01:23:16');

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
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
