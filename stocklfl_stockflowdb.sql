SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `categorias_gasto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `categorias_gasto` (`id`, `nombre`) VALUES
(2, 'Empaque'),
(1, 'Logistica'),
(5, 'Otros'),
(4, 'Plataformas'),
(3, 'Publicidad');

CREATE TABLE `categorias_producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `categorias_producto` (`id`, `nombre`) VALUES
(2, 'Accesorios'),
(4, 'Hogar'),
(1, 'Indumentaria'),
(3, 'Tecnologia');

CREATE TABLE `configuracion` (
  `clave` varchar(60) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('empresa', 'NovaTech Solutions'),
('moneda', '$'),
('stock_minimo_default', '5');

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `descripcion` varchar(160) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `gastos` (`id`, `categoria_id`, `usuario_id`, `descripcion`, `monto`, `fecha`) VALUES
(1, 1, 1, 'Envios julio', 3200.00, '2026-07-31'),
(2, 3, 1, 'Campana Instagram', 4500.00, '2026-07-20'),
(3, 2, 1, 'Cajas y etiquetas', 1800.00, '2026-08-01');

CREATE TABLE `movimientos_stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('ingreso','egreso','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_resultante` int(11) NOT NULL,
  `motivo` varchar(160) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `movimientos_stock` (`id`, `producto_id`, `usuario_id`, `tipo`, `cantidad`, `stock_resultante`, `motivo`, `fecha`) VALUES
(1, 1, 3, 'ingreso', 50, 50, 'Compra inicial', '2026-07-01 09:00:00'),
(2, 1, 2, 'egreso', 3, 47, 'Venta #1', '2026-07-10 10:15:00'),
(3, 4, 3, 'ingreso', 20, 20, 'Reposicion', '2026-07-05 11:00:00'),
(4, 6, 1, 'ingreso', 60, 60, 'Alta de producto', '2026-10-03 20:36:10'),
(5, 4, 1, 'egreso', 3, 15, 'Venta #4', '2026-10-03 20:37:27'),
(6, 4, 1, 'egreso', 1, 14, 'Venta #5', '2026-10-05 20:31:23'),
(7, 7, 1, 'ingreso', 10, 10, 'Alta de producto', '2026-11-04 16:31:13'),
(8, 8, 1, 'ingreso', 15, 15, 'Alta de producto', '2026-11-04 16:37:11'),
(9, 10, 1, 'ingreso', 20, 20, 'Alta de producto', '2026-11-04 16:44:24'),
(10, 8, 1, 'egreso', 1, 14, 'Venta #6', '2026-11-04 16:47:44'),
(11, 11, 1, 'ingreso', 25, 25, 'Alta de producto', '2026-11-04 17:15:47'),
(12, 7, 1, 'egreso', 1, 9, 'Venta #17', '2026-11-06 15:09:47'),
(13, 7, 1, 'egreso', 7, 2, 'Venta #18', '2026-11-06 15:09:55');

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `tipo` enum('stock_minimo','venta','sistema') NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `notificaciones` (`id`, `tipo`, `mensaje`, `leida`, `fecha`) VALUES
(1, 'stock_minimo', 'El producto Gorra bordada esta por debajo del stock minimo', 0, '2026-09-16 17:47:39'),
(2, 'stock_minimo', 'El producto Mate ceramica esta por debajo del stock minimo', 0, '2026-09-16 17:47:39'),
(3, 'venta', 'Nueva venta #4 por $4,770.00', 0, '2026-10-03 20:37:27'),
(4, 'venta', 'Nueva venta #5 por $1,590.00', 0, '2026-10-05 20:31:23'),
(5, 'venta', 'Nueva venta #6 por $1,000.00', 0, '2026-11-04 16:47:44'),
(6, 'venta', 'Nueva venta #17 por $600.00', 0, '2026-11-06 15:09:47'),
(7, 'venta', 'Nueva venta #18 por $4,200.00', 0, '2026-11-06 15:09:55'),
(8, 'stock_minimo', 'Stock minimo alcanzado: Remera Oversize M (2)', 0, '2026-11-06 15:09:55');

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(40) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `unidad` varchar(20) NOT NULL DEFAULT 'unidad',
  `precio_costo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `categoria_id`, `unidad`, `precio_costo`, `precio_venta`, `stock`, `stock_minimo`, `imagen`, `activo`, `creado`) VALUES
(1, 'SF-001', 'Remera algodon', 1, 'unidad', 350.00, 790.00, 40, 10, NULL, 0, '2026-09-16 17:47:39'),
(2, 'SF-002', 'Buzo canguro', 1, 'unidad', 900.00, 1990.00, 12, 5, NULL, 0, '2026-09-16 17:47:39'),
(3, 'SF-003', 'Gorra bordada', 2, 'unidad', 250.00, 650.00, 4, 6, NULL, 0, '2026-09-16 17:47:39'),
(4, 'SF-004', 'Auriculares BT', 3, 'unidad', 780.00, 1590.00, 14, 5, NULL, 0, '2026-09-16 17:47:39'),
(5, 'SF-005', 'Mate ceramica', 4, 'unidad', 420.00, 980.00, 3, 8, NULL, 0, '2026-09-16 17:47:39'),
(6, '10', 'Prueba', 2, 'unidad', 60.00, 300.00, 60, 5, NULL, 0, '2026-10-03 20:36:10'),
(7, 'AF 7895', 'Remera Oversize M', 1, 'unidad', 399.00, 600.00, 2, 5, 'img/productos/p7_1793809874.jpg', 1, '2026-11-04 16:31:13'),
(8, 'AF 1065', 'Buzo Oversize L', 1, 'unidad', 499.00, 1000.00, 14, 5, 'img/productos/p8_1793810232.jpg', 1, '2026-11-04 16:37:11'),
(10, 'AF 8585', 'Pantalon Baggy 40', 1, 'unidad', 900.00, 2500.00, 20, 5, 'img/productos/p10_1793810665.jpg', 1, '2026-11-04 16:44:24'),
(11, 'AF 1891', 'Air Force one', 1, 'unidad', 2000.00, 4500.00, 25, 5, 'img/productos/p11_1793812572.jpg', 1, '2026-11-04 17:15:47');

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','vendedor','repositor','cliente') NOT NULL DEFAULT 'vendedor',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `activo`, `creado`) VALUES
(1, 'Administrador', 'admin@stockflow.com', '$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK', 'admin', 1, '2026-09-16 17:47:39'),
(2, 'Vendedor Demo', 'vendedor@stockflow.com', '$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK', 'vendedor', 1, '2026-09-16 17:47:39'),
(3, 'Repositor Demo', 'repositor@stockflow.com', '$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK', 'repositor', 1, '2026-09-16 17:47:39'),
(4, 'Cliente Demo', 'cliente@stockflow.com', '$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK', 'cliente', 1, '2026-09-16 17:47:39'),
(5, 'Nacho Rocha', 'contact.nachoz@gmail.com', '$2y$10$beX3ug1HhxMomBzh1UOWMOtRS/Zp/b3RvcC3mHj25PJxkIL.1yCUG', 'admin', 0, '2026-10-05 00:34:16'),
(6, 'Thiago SIlveira', 'th1891@gmail.com', '$2y$10$HUY5zLtre3rM3soi95bvHuO4hJ/g9eDmh6m8vkEmNUBBbJ06H4jWm', 'admin', 0, '2026-10-22 19:06:00');

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `cliente` varchar(120) DEFAULT NULL,
  `canal` varchar(60) NOT NULL DEFAULT 'web',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `ventas` (`id`, `usuario_id`, `cliente`, `canal`, `total`, `costo_total`, `estado`, `fecha`) VALUES
(1, 2, 'Ana Perez', 'tienda_online', 2370.00, 1050.00, 'entregado', '2026-07-10 10:15:00'),
(2, 2, 'Luis Gomez', 'marketplace', 1590.00, 780.00, 'enviado', '2026-07-18 16:40:00'),
(3, 2, 'Sofia Diaz', 'redes', 1630.00, 670.00, 'enviado', '2026-08-02 12:05:00'),
(4, 1, 'Prueba', 'tienda_online', 4770.00, 2340.00, 'enviado', '2026-10-03 20:37:27'),
(5, 1, '', 'web', 1590.00, 780.00, 'enviado', '2026-10-05 20:31:23'),
(6, 1, '', 'web', 1000.00, 499.00, 'enviado', '2026-11-04 16:47:44'),
(17, 1, NULL, 'web', 600.00, 399.00, 'pendiente', '2026-11-06 15:09:47'),
(18, 1, NULL, 'web', 4200.00, 2793.00, 'pendiente', '2026-11-06 15:09:55');

CREATE TABLE `venta_detalle` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `costo_unitario` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `venta_detalle` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `costo_unitario`) VALUES
(1, 1, 1, 3, 790.00, 350.00),
(2, 2, 4, 1, 1590.00, 780.00),
(3, 3, 3, 1, 650.00, 250.00),
(4, 3, 5, 1, 980.00, 420.00),
(5, 4, 4, 3, 1590.00, 780.00),
(6, 5, 4, 1, 1590.00, 780.00),
(7, 6, 8, 1, 1000.00, 499.00),
(8, 17, 7, 1, 600.00, 399.00),
(9, 18, 7, 7, 600.00, 399.00);

ALTER TABLE `categorias_gasto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `categorias_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`clave`);

ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `usuario_id` (`usuario_id`);

ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `categoria_id` (`categoria_id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

ALTER TABLE `categorias_gasto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `categorias_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `movimientos_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `venta_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_gasto` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gastos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `movimientos_stock`
  ADD CONSTRAINT `movimientos_stock_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_stock_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_producto` (`id`) ON DELETE SET NULL;

ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL;

COMMIT;
