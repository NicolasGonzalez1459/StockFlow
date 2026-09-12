StockFlow - Sistema de Gestion de Stock y Ventas
CETP-UTU | Polo Educativo Tecnologico | BT Informatica 2026
Empresa ficticia: HuskyTech

Estructura:
  css/   estilos
  html/  paginas (index.html = login)
  js/    logica de cliente e idiomas (ES/EN)
  php/   API en PHP + PDO/MySQL
  stock.sql  base de datos

Instalacion:
1. Copiar la carpeta StockFlow en htdocs (XAMPP) o www (WAMP).
2. Importar stock.sql en phpMyAdmin.
3. Ajustar credenciales en php/config.php si es necesario.
4. Abrir http://localhost/StockFlow/html/index.html

Usuarios demo (password: 123456)
  admin@stockflow.com      Administrador
  vendedor@stockflow.com   Vendedor
  repositor@stockflow.com  Repositor
  cliente@stockflow.com    Cliente (portal opcional)

Portal cliente (Mis pedidos):
  El cliente puede recorrer el catalogo, agregar productos al carrito
  y confirmar la compra. La compra queda registrada como una venta
  y aparece de inmediato en su listado de pedidos.

Fotos de producto:
  Desde Inventario (rol admin) se puede cargar o reemplazar la foto
  de cualquier producto, incluidos los que todavia no tienen imagen.
  Las fotos se guardan en img/productos/, la carpeta debe tener
  permiso de escritura para el servidor web.
