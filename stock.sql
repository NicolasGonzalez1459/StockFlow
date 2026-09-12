DROP DATABASE IF EXISTS stockflow;
CREATE DATABASE stockflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockflow;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','vendedor','repositor','cliente') NOT NULL DEFAULT 'vendedor',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias_producto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE categorias_gasto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(40) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  categoria_id INT NULL,
  unidad VARCHAR(20) NOT NULL DEFAULT 'unidad',
  precio_costo DECIMAL(12,2) NOT NULL DEFAULT 0,
  precio_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 0,
  imagen VARCHAR(255) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias_producto(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE movimientos_stock (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto_id INT NOT NULL,
  usuario_id INT NULL,
  tipo ENUM('ingreso','egreso','ajuste') NOT NULL,
  cantidad INT NOT NULL,
  stock_resultante INT NOT NULL,
  motivo VARCHAR(160) NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  cliente VARCHAR(120) NULL,
  canal VARCHAR(60) NOT NULL DEFAULT 'web',
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  costo_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  estado ENUM('pendiente','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE venta_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  producto_id INT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(12,2) NOT NULL,
  costo_unitario DECIMAL(12,2) NOT NULL DEFAULT 0,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE gastos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NULL,
  usuario_id INT NULL,
  descripcion VARCHAR(160) NOT NULL,
  monto DECIMAL(12,2) NOT NULL,
  fecha DATE NOT NULL,
  FOREIGN KEY (categoria_id) REFERENCES categorias_gasto(id) ON DELETE SET NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('stock_minimo','venta','sistema') NOT NULL,
  mensaje VARCHAR(255) NOT NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE configuracion (
  clave VARCHAR(60) PRIMARY KEY,
  valor VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre,email,password,rol) VALUES
('Administrador','admin@stockflow.com','$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK','admin'),
('Vendedor Demo','vendedor@stockflow.com','$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK','vendedor'),
('Repositor Demo','repositor@stockflow.com','$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK','repositor'),
('Cliente Demo','cliente@stockflow.com','$2a$10$9seUwnS783Su4IOgYuA7BOGmXBTYGpGhpll841pBELOxZZEq.zarK','cliente');

INSERT INTO categorias_producto (nombre) VALUES ('Indumentaria'),('Accesorios'),('Tecnologia'),('Hogar');
INSERT INTO categorias_gasto (nombre) VALUES ('Logistica'),('Empaque'),('Publicidad'),('Plataformas'),('Otros');

INSERT INTO productos (codigo,nombre,categoria_id,unidad,precio_costo,precio_venta,stock,stock_minimo) VALUES
('SF-001','Remera algodon',1,'unidad',350.00,790.00,40,10),
('SF-002','Buzo canguro',1,'unidad',900.00,1990.00,12,5),
('SF-003','Gorra bordada',2,'unidad',250.00,650.00,4,6),
('SF-004','Auriculares BT',3,'unidad',780.00,1590.00,18,5),
('SF-005','Mate ceramica',4,'unidad',420.00,980.00,3,8);

INSERT INTO ventas (usuario_id,cliente,canal,total,costo_total,estado,fecha) VALUES
(2,'Ana Perez','tienda_online',2370.00,1050.00,'entregado','2026-07-10 10:15:00'),
(2,'Luis Gomez','marketplace',1590.00,780.00,'enviado','2026-07-18 16:40:00'),
(2,'Sofia Diaz','redes',1630.00,670.00,'pendiente','2026-08-02 12:05:00');

INSERT INTO venta_detalle (venta_id,producto_id,cantidad,precio_unitario,costo_unitario,subtotal) VALUES
(1,1,3,790.00,350.00,2370.00),
(2,4,1,1590.00,780.00,1590.00),
(3,3,1,650.00,250.00,650.00),
(3,5,1,980.00,420.00,980.00);

INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo,fecha) VALUES
(1,3,'ingreso',50,50,'Compra inicial','2026-07-01 09:00:00'),
(1,2,'egreso',3,47,'Venta #1','2026-07-10 10:15:00'),
(4,3,'ingreso',20,20,'Reposicion','2026-07-05 11:00:00');

INSERT INTO gastos (categoria_id,usuario_id,descripcion,monto,fecha) VALUES
(1,1,'Envios julio',3200.00,'2026-07-31'),
(3,1,'Campana Instagram',4500.00,'2026-07-20'),
(2,1,'Cajas y etiquetas',1800.00,'2026-08-01');

INSERT INTO notificaciones (tipo,mensaje) VALUES
('stock_minimo','El producto Gorra bordada esta por debajo del stock minimo'),
('stock_minimo','El producto Mate ceramica esta por debajo del stock minimo');

INSERT INTO configuracion (clave,valor) VALUES
('empresa','NovaTech Solutions'),('moneda','$'),('stock_minimo_default','5');

ALTER TABLE productos ADD COLUMN IF NOT EXISTS imagen VARCHAR(255) NULL;
