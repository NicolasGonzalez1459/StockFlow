StockFlow
CETP-UTU | Polo Educativo Tecnologico | BT Informatica 2026
Empresa ficticia: NovaTech Solutions

Descripcion:
  Sistema de gestion de stock, ventas, gastos, usuarios y reportes,
  desarrollado en PHP puro con una arquitectura simple por capas.
  El proyecto incluye un panel administrativo y un portal cliente.

Estructura actual:
  api/                 endpoints y servicios de la API
  css/                 estilos del sistema
  img/                 recursos graficos y fotos de productos
  js/                  scripts del frontend
  php/                 configuracion, autoload, nucleo, DAO y paginas
  index.php            punto de entrada principal
  stocklfl_stockflowdb.sql   script de base de datos
  README.txt           documentacion del proyecto

Arquitectura:
  - Las vistas se resuelven desde PHP y la carpeta php/paginas.
  - La logica de negocio y validaciones se encuentran en php/core.
  - Los accesos a base de datos quedan encapsulados en php/dao.
  - La configuracion central se define en php/config.php.
  - La API REST para sesion y autenticacion se encuentra en api/.

Requisitos:
  - XAMPP, WAMP o un servidor con PHP 8+ y MySQL/MariaDB.
  - Permisos de escritura en la carpeta img/ para cargar imagenes.

Instalacion:
1. Copiar la carpeta StockFlow en htdocs (XAMPP) o www (WAMP).
2. Importar el archivo stocklfl_stockflowdb.sql en phpMyAdmin o MySQL.
3. Revisar y ajustar las credenciales de base de datos en php/config.php si es necesario.
4. Abrir la aplicacion en: http://localhost/StockFlow/index.php

Usuarios demo (password: 123456)
  admin@stockflow.com       Administrador
  vendedor@stockflow.com    Vendedor
  repositor@stockflow.com   Repositor
  cliente@stockflow.com     Cliente (portal)

Funciones principales:
  - Gestion de productos y categorias
  - Control de stock minimo y movimientos de stock
  - Registro de ventas con detalle por producto
  - Registro de gastos y categorias de gasto
  - Autenticacion por sesion PHP
  - Notificaciones de stock bajo y ventas recientes
  - Carga y reemplazo de imagenes de productos

Portal cliente (Mis pedidos):
  El cliente recorre el catalogo, agrega productos a un carrito
  guardado en la sesion PHP ($_SESSION) y confirma la compra.
  La compra queda registrada como una venta y aparece de inmediato
  en su listado de pedidos.

Fotos de producto:
  Desde Inventario (rol admin) se puede cargar o reemplazar la foto
  de cualquier producto. Las fotos se guardan en img/ y deben poder
  ser escritas por el servidor web.

API de sesion:
  La aplicacion expone endpoints basicos bajo api/ para iniciar sesion,
  consultar el usuario autenticado y cerrar sesion.
