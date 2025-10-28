-- Primero, otorga los privilegios necesarios para crear funciones y ejecutar vistas
GRANT CREATE ROUTINE, SHOW VIEW ON `if0_40275581_bdbolopdrive`.* TO 'if0_40275581'@'sql100.infinityfree.com' WITH GRANT OPTION;

-- Aplica los cambios
FLUSH PRIVILEGES;

-- Ahora, otorga los privilegios específicos para el uso que necesitas
GRANT CREATE ROUTINE, SHOW VIEW ON `if0_40275581_bdbolopdrive`.* TO 'if0_40275581'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Luego, crea la función 'haversine_km'
DELIMITER $$
CREATE DEFINER=`if0_40275581`@`%` FUNCTION `haversine_km` (`lat1` DOUBLE, `lng1` DOUBLE, `lat2` DOUBLE, `lng2` DOUBLE) RETURNS DOUBLE DETERMINISTIC BEGIN
  RETURN 6371 * ACOS(
    COS(RADIANS(lat1)) * COS(RADIANS(lat2)) *
    COS(RADIANS(lng2) - RADIANS(lng1)) +
    SIN(RADIANS(lat1)) * SIN(RADIANS(lat2))
  );
END$$
DELIMITER ;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `chat_history`
CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `interacciones`
CREATE TABLE `interacciones` (
  `id` bigint(20) NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `detalle` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalle`)),
  `ocurrido_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcado de datos para la tabla `interacciones`
INSERT INTO `interacciones` (`id`, `tipo`, `usuario_id`, `detalle`, `ocurrido_en`) VALUES
(1, 'login_ok', 1, '[]', '2025-10-18 06:52:38'),
(2, 'logout', 1, '[]', '2025-10-18 06:53:14'),
(3, 'login_ok', 1, '[]', '2025-10-18 07:00:05'),
(4, 'logout', 1, '[]', '2025-10-18 07:00:23'),
(5, 'login_fail', NULL, '{\"email\":\"user@demo.com\"}', '2025-10-18 07:30:10'),
(6, 'login_ok', 4, '[]', '2025-10-18 07:30:58'),
(7, 'logout', 4, '[]', '2025-10-18 07:31:13'),
(8, 'login_fail', NULL, '{\"email\":\"user@demo.com\"}', '2025-10-18 07:31:35'),
(9, 'login_fail', NULL, '{\"email\":\"user@demo.com\"}', '2025-10-18 07:31:46'),
(10, 'login_fail', NULL, '{\"email\":\"user@demo.com\"}', '2025-10-18 07:32:12');

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `ofertas`
CREATE TABLE `ofertas` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `conductor_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` enum('propuesta','aceptada','rechazada','expirada') DEFAULT 'propuesta',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `otp_codes`
CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `purpose` enum('reset','login','verify') NOT NULL,
  `code_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `otp_codes`
INSERT INTO `otp_codes` (`id`, `user_id`, `email`, `purpose`, `code_hash`, `expires_at`, `attempts`, `max_attempts`, `used_at`, `created_at`) VALUES
(1, 18, 'n3lmijo@gmail.com', 'verify', 'b1ce00779750fd95eb59e38ef80eb8bb38a0f8045b42b76854ed23d234d0ce2b', '2025-10-27 08:16:18', 0, 5, '2025-10-27 01:06:35', '2025-10-27 01:06:18'),
(3, 18, 'n3lmijo@gmail.com', 'reset', '5d087174666379481354a82c4c69576900cd6ed1e2c4a31d9ce8aa8da8d57bc1', '2025-10-27 08:19:39', 0, 5, '2025-10-27 01:10:03', '2025-10-27 01:09:39');

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `usuarios`
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email_verificado_at` datetime DEFAULT NULL,
  `twofa_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `rol_id` tinyint(4) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `perfiles_conductor`
CREATE TABLE `perfiles_conductor` (
  `usuario_id` int(11) NOT NULL,
  `licencia_num` varchar(50) DEFAULT NULL,
  `licencia_vence` date DEFAULT NULL,
  `vehiculo_modelo` varchar(60) DEFAULT NULL,
  `vehiculo_color` varchar(30) DEFAULT NULL,
  `vehiculo_placa` varchar(30) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 0,
  `rating_promedio` decimal(3,2) DEFAULT 5.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- AUTO_INCREMENT de las tablas volcadas
ALTER TABLE `chat_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `interacciones` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
ALTER TABLE `ofertas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `otp_codes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
ALTER TABLE `password_resets` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `solicitudes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `usuarios` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `viajes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

-- Restricciones para tablas volcadas
ALTER TABLE `ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `ofertas_ibfk_2` FOREIGN KEY (`conductor_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `perfiles_conductor`
  ADD CONSTRAINT `perfiles_conductor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `perfiles_usuario`
  ADD CONSTRAINT `perfiles_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `ubicaciones`
  ADD CONSTRAINT `ubicaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

ALTER TABLE `viajes`
  ADD CONSTRAINT `viajes_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `viajes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `viajes_ibfk_3` FOREIGN KEY (`conductor_id`) REFERENCES `usuarios` (`id`);

COMMIT;
