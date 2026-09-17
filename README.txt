StockFlow
CETP-UTU | Polo Educativo Tecnologico | BT Informatica 2026
Empresa ficticia: NovaTech Solutions

Descripcion:
  Sistema de gestion de stock, ventas y reportes desarrollado en PHP puro,
  orientado a objetos y con arquitectura simple por capas.

Estructura actual:
  config/        configuracion general: constantes, autoload y sesion
  clases/        logica de negocio y entidades principales
  dao/           acceso a datos y consultas SQL
  parciales/     cabecera.php y pie.php (layout compartido)
  css/           estilos del sistema
  img/           recursos graficos y fotos de productos
  *.php          pantallas/ventanas del sistema
  stock.sql      script de base de datos

Arquitectura:
  - Las pantallas PHP representan las vistas del sistema.
  - Las clases en clases/ contienen la logica del negocio.
  - Las clases en dao/ encapsulan las consultas a la base de datos.
  - La configuracion central queda en config/config.php.

Instalacion:
1. Copiar la carpeta StockFlow2 en htdocs (XAMPP) o www (WAMP).
2. Importar stock.sql en phpMyAdmin.
3. Ajustar credenciales en config/config.php si es necesario.
4. Abrir http://localhost/StockFlow2/index.php

Usuarios demo (password: 123456)
  admin@stockflow.com      Administrador
  vendedor@stockflow.com   Vendedor
  repositor@stockflow.com  Repositor
  cliente@stockflow.com    Cliente (portal)

Portal cliente (Mis pedidos):
  El cliente recorre el catalogo, agrega productos a un carrito
  guardado en la sesion PHP ($_SESSION) y confirma la compra.
  La compra queda registrada como una venta y aparece de inmediato
  en su listado de pedidos.

Fotos de producto:
  Desde Inventario (rol admin) se puede cargar o reemplazar la foto
  de cualquier producto. Las fotos se guardan en img/productos/, la
  carpeta debe tener permiso de escritura para el servidor web.
