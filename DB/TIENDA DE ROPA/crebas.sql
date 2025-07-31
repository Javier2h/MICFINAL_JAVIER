/*==============================================================*/
/* TABLAS SIN CLAVES FORÁNEAS                                   */
/*==============================================================*/

/* CLIENTES */
CREATE TABLE CLIENTES (
   ID_CLIENTE INT PRIMARY KEY,
   NOMBRE VARCHAR(100),
   APELLIDO VARCHAR(100),
   DIRECCION VARCHAR(100),
   TELEFONO VARCHAR(15)
);

/* CATEGORIAS */
CREATE TABLE CATEGORIAS (
   ID_CATEGORIA INT PRIMARY KEY,
   NOMBRE_CATEGORIA VARCHAR(100)
);

/* PAGOS */
CREATE TABLE PAGOS (
   ID_PAGO INT PRIMARY KEY,
   METODO_PAGO VARCHAR(50),
   ESTADO VARCHAR(50),
   TOTAL DECIMAL(10,2)
);

/*==============================================================*/
/* TABLAS CON CLAVES FORÁNEAS                                   */
/*==============================================================*/

/* PRODUCTOS */
CREATE TABLE PRODUCTOS (
   ID_PRODUCTO INT PRIMARY KEY,
   NOMBRE_PRODUCTO VARCHAR(100),
   PRECIO DECIMAL(10,2),
   ID_CATEGORIA INT,
   FOREIGN KEY (ID_CATEGORIA) REFERENCES CATEGORIAS(ID_CATEGORIA)
);

/* INVENTARIO */
CREATE TABLE INVENTARIO (
   ID_INVENTARIO INT PRIMARY KEY,
   ID_PRODUCTO INT,
   STOCK INT,
   FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTOS(ID_PRODUCTO)
);

/* PEDIDOS */
CREATE TABLE PEDIDOS (
   ID_PEDIDO INT PRIMARY KEY,
   FECHA DATETIME,
   ID_CLIENTE INT,
   ID_PAGO INT,
   FOREIGN KEY (ID_CLIENTE) REFERENCES CLIENTES(ID_CLIENTE),
   FOREIGN KEY (ID_PAGO) REFERENCES PAGOS(ID_PAGO)
);

/* DETALLE_PEDIDOS */
CREATE TABLE DETALLE_PEDIDOS (
   ID_DETALLE INT PRIMARY KEY,
   ID_PEDIDO INT,
   ID_PRODUCTO INT,
   CANTIDAD INT,
   SUBTOTAL DECIMAL(10,2),
   FOREIGN KEY (ID_PEDIDO) REFERENCES PEDIDOS(ID_PEDIDO),
   FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTOS(ID_PRODUCTO)
);


-- CLIENTES
INSERT INTO CLIENTES VALUES
(1, 'Lucía', 'Pérez', 'Av. América 101', '0991122334'),
(2, 'Carlos', 'Ramírez', 'Calle de los Shyris', '0987654321'),
(3, 'María', 'Lozano', 'Av. La República', '0977889911'),
(4, 'Juan', 'Gómez', 'El Ejido sector norte', '0999988776');

-- CATEGORIAS
INSERT INTO CATEGORIAS VALUES
(1, 'Camisetas'),
(2, 'Jeans'),
(3, 'Chaquetas'),
(4, 'Zapatos');

-- PRODUCTOS
INSERT INTO PRODUCTOS VALUES
(1, 'Camiseta básica negra', 15.99, 1),
(2, 'Jeans azul oscuro', 39.50, 2),
(3, 'Chaqueta impermeable', 55.00, 3),
(4, 'Zapatos deportivos', 29.99, 4);

-- INVENTARIO
INSERT INTO INVENTARIO VALUES
(1, 1, 120),
(2, 2, 85),
(3, 3, 50),
(4, 4, 200);

-- PAGOS
INSERT INTO PAGOS VALUES
(1, 'Tarjeta crédito', 'Completado', 70.98),
(2, 'Transferencia', 'Pendiente', 39.50),
(3, 'Efectivo', 'Completado', 29.99),
(4, 'PayPal', 'Completado', 15.99);

-- PEDIDOS
INSERT INTO PEDIDOS VALUES
(1, '2025-07-22 14:30:00', 2, 1),
(2, '2025-07-21 09:00:00', 4, 2),
(3, '2025-07-19 16:45:00', 1, 3),
(4, '2025-07-18 11:15:00', 3, 4);

-- DETALLE_PEDIDOS
INSERT INTO DETALLE_PEDIDOS VALUES
(1, 1, 2, 1, 39.50),
(2, 2, 4, 1, 29.99),
(3, 3, 3, 1, 55.00),
(4, 4, 1, 1, 15.99);