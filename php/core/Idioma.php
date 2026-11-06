<?php

final class Idioma
{
    private const DICCIONARIO = [
        'es' => [
            'tagline' => 'Sistema de Gestion de Stock y Ventas',
            'email' => 'Correo', 'pass' => 'Contrasena', 'enter' => 'Entrar', 'logout' => 'Salir',
            'dashboard' => 'Dashboard', 'inventory' => 'Inventario', 'sales' => 'Ventas', 'expenses' => 'Gastos',
            'reports' => 'Reportes', 'users' => 'Usuarios', 'alerts' => 'Alertas', 'portal' => 'Mis pedidos',
            'from' => 'Desde', 'to' => 'Hasta', 'apply' => 'Aplicar', 'export' => 'Exportar reporte', 'new' => 'Nuevo', 'save' => 'Guardar',
            'cancel' => 'Cancelar', 'edit' => 'Editar', 'delete' => 'Eliminar', 'search' => 'Buscar',
            'salesTotal' => 'Ventas del periodo', 'salesCount' => 'Cantidad de ventas', 'expensesTotal' => 'Gastos',
            'margin' => 'Margen estimado', 'critical' => 'Stock critico', 'topProducts' => 'Productos mas vendidos',
            'evolution' => 'Evolucion de ventas', 'composition' => 'Composicion de ingresos',
            'code' => 'Codigo', 'name' => 'Nombre', 'category' => 'Categoria',
            'unit' => 'Unidad', 'cost' => 'Precio costo', 'price' => 'Precio venta', 'stock' => 'Stock', 'min' => 'Stock minimo',
            'actions' => 'Acciones', 'movements' => 'Movimientos', 'type' => 'Tipo', 'qty' => 'Cantidad', 'reason' => 'Motivo',
            'date' => 'Fecha', 'user' => 'Usuario', 'product' => 'Producto', 'add' => 'Agregar', 'client' => 'Cliente',
            'channel' => 'Canal', 'total' => 'Total', 'status' => 'Estado', 'seller' => 'Vendedor', 'detail' => 'Detalle',
            'register' => 'Registrar venta', 'amount' => 'Monto', 'description' => 'Descripcion', 'role' => 'Rol',
            'active' => 'Activo', 'noData' => 'Sin datos',
            'profit' => 'Rentabilidad', 'units' => 'Unidades', 'revenue' => 'Ingresos', 'costs' => 'Costos', 'valorized' => 'Valorizado',
            'badCreds' => 'Credenciales incorrectas', 'notifications' => 'Notificaciones',
            'buyHere' => 'Poner una compra aqui', 'cart' => 'Carrito', 'addCart' => 'Agregar carrito', 'confirmBuy' => 'Confirmar compra',
            'myOrders' => 'Mis pedidos', 'noImage' => 'Sin foto', 'outOfStock' => 'Sin stock', 'emptyCart' => 'El carrito esta vacio',
            'removeItem' => 'Quitar', 'availableStock' => 'Disponibles', 'photo' => 'Foto',
            'dupCode' => 'Ese codigo ya esta usado por otro producto',
            'yes' => 'Si', 'no' => 'No', 'confirmDelete' => 'Seguro que desea continuar?',
            'repSales' => 'Ventas', 'repStock' => 'Stock', 'repExpenses' => 'Gastos', 'repProfit' => 'Rentabilidad',
            'generated' => 'Generado el',
            'stock_insuficiente' => 'Stock insuficiente', 'codigo_duplicado' => 'Ese codigo ya esta usado por otro producto',
            'sin_permiso' => 'Sin permiso', 'no_autenticado' => 'Sesion vencida', 'sin_items' => 'No hay productos',
            'archivo' => 'Archivo invalido', 'formato' => 'Formato no permitido', 'tamano' => 'La imagen supera 3 MB',
            'subida' => 'No se pudo guardar la imagen', 'producto' => 'Producto inexistente', 'db' => 'Error de base de datos',
            'error' => 'Ocurrio un error', 'datos' => 'Datos invalidos o incompletos', 'email_duplicado' => 'Ese correo ya esta registrado',
            'not_found' => 'Pagina no encontrada', 'connError' => 'No se pudo conectar con la base de datos',
            'ok' => 'Operacion exitosa', 'creado' => 'Registro creado correctamente', 'actualizado' => 'Registro actualizado correctamente',
            'eliminado' => 'Registro eliminado correctamente', 'no_encontrado' => 'Recurso no encontrado', 'metodo' => 'Metodo no permitido',
            'formato_json' => 'El contenido debe enviarse como application/json', 'credenciales' => 'Credenciales incorrectas',
            'sesion_iniciada' => 'Sesion iniciada', 'sesion_cerrada' => 'Sesion cerrada', 'netError' => 'No se pudo conectar con el servidor',
            'no_autoeliminar' => 'No puede dar de baja su propio usuario', 'imagen_subida' => 'Imagen guardada correctamente',
            'markRead' => 'Marcar leida', 'status_actualizado' => 'Estado actualizado', 'saleDone' => 'Venta registrada correctamente',
            'purchaseDone' => 'Compra realizada correctamente', 'edit_cancel' => 'Cancelar edicion', 'noProducts' => 'Sin productos disponibles',
            'pending' => 'pendiente', 'shipped' => 'enviado', 'delivered' => 'entregado', 'cancelled' => 'cancelado',
            'ingreso' => 'ingreso', 'egreso' => 'egreso', 'ajuste' => 'ajuste',
        ],
        'en' => [
            'tagline' => 'Stock and Sales Management System',
            'email' => 'Email', 'pass' => 'Password', 'enter' => 'Enter', 'logout' => 'Log out',
            'dashboard' => 'Dashboard', 'inventory' => 'Inventory', 'sales' => 'Sales', 'expenses' => 'Expenses',
            'reports' => 'Reports', 'users' => 'Users', 'alerts' => 'Alerts', 'portal' => 'My orders',
            'from' => 'From', 'to' => 'To', 'apply' => 'Apply', 'export' => 'Export report', 'new' => 'New', 'save' => 'Save',
            'cancel' => 'Cancel', 'edit' => 'Edit', 'delete' => 'Delete', 'search' => 'Search',
            'salesTotal' => 'Period sales', 'salesCount' => 'Sales count', 'expensesTotal' => 'Expenses',
            'margin' => 'Estimated margin', 'critical' => 'Critical stock', 'topProducts' => 'Top selling products',
            'evolution' => 'Sales evolution', 'composition' => 'Revenue composition',
            'code' => 'Code', 'name' => 'Name', 'category' => 'Category',
            'unit' => 'Unit', 'cost' => 'Cost price', 'price' => 'Sale price', 'stock' => 'Stock', 'min' => 'Min stock',
            'actions' => 'Actions', 'movements' => 'Movements', 'type' => 'Type', 'qty' => 'Quantity', 'reason' => 'Reason',
            'date' => 'Date', 'user' => 'User', 'product' => 'Product', 'add' => 'Add', 'client' => 'Client',
            'channel' => 'Channel', 'total' => 'Total', 'status' => 'Status', 'seller' => 'Seller', 'detail' => 'Detail',
            'register' => 'Register sale', 'amount' => 'Amount', 'description' => 'Description', 'role' => 'Role',
            'active' => 'Active', 'noData' => 'No data',
            'profit' => 'Profitability', 'units' => 'Units', 'revenue' => 'Revenue', 'costs' => 'Costs', 'valorized' => 'Valued stock',
            'badCreds' => 'Invalid credentials', 'notifications' => 'Notifications',
            'buyHere' => 'Place an order here', 'cart' => 'Cart', 'addCart' => 'Add to cart', 'confirmBuy' => 'Confirm purchase',
            'myOrders' => 'My orders', 'noImage' => 'No photo', 'outOfStock' => 'Out of stock', 'emptyCart' => 'Your cart is empty',
            'removeItem' => 'Remove', 'availableStock' => 'Available', 'photo' => 'Photo',
            'dupCode' => 'That code is already used by another product',
            'yes' => 'Yes', 'no' => 'No', 'confirmDelete' => 'Are you sure you want to continue?',
            'repSales' => 'Sales', 'repStock' => 'Stock', 'repExpenses' => 'Expenses', 'repProfit' => 'Profit',
            'generated' => 'Generated on',
            'stock_insuficiente' => 'Not enough stock', 'codigo_duplicado' => 'That code is already used by another product',
            'sin_permiso' => 'Not allowed', 'no_autenticado' => 'Session expired', 'sin_items' => 'No products selected',
            'archivo' => 'Invalid file', 'formato' => 'Format not allowed', 'tamano' => 'Image is larger than 3 MB',
            'subida' => 'The image could not be saved', 'producto' => 'Product not found', 'db' => 'Database error',
            'error' => 'An error occurred', 'datos' => 'Invalid or incomplete data', 'email_duplicado' => 'That email is already registered',
            'not_found' => 'Page not found', 'connError' => 'Could not connect to the database',
            'ok' => 'Operation successful', 'creado' => 'Record created successfully', 'actualizado' => 'Record updated successfully',
            'eliminado' => 'Record deleted successfully', 'no_encontrado' => 'Resource not found', 'metodo' => 'Method not allowed',
            'formato_json' => 'The content must be sent as application/json', 'credenciales' => 'Invalid credentials',
            'sesion_iniciada' => 'Signed in', 'sesion_cerrada' => 'Signed out', 'netError' => 'Could not reach the server',
            'no_autoeliminar' => 'You cannot deactivate your own user', 'imagen_subida' => 'Image saved successfully',
            'markRead' => 'Mark as read', 'status_actualizado' => 'Status updated', 'saleDone' => 'Sale registered successfully',
            'purchaseDone' => 'Purchase completed successfully', 'edit_cancel' => 'Cancel edit', 'noProducts' => 'No products available',
            'pending' => 'pending', 'shipped' => 'shipped', 'delivered' => 'delivered', 'cancelled' => 'cancelled',
            'ingreso' => 'stock in', 'egreso' => 'stock out', 'ajuste' => 'adjust',
        ],
    ];

    public static function actual(): string
    {
        $cookie = $_COOKIE['stockflow_lang'] ?? 'es';
        return isset(self::DICCIONARIO[$cookie]) ? $cookie : 'es';
    }

    public static function establecer(string $idioma): void
    {
        if (!isset(self::DICCIONARIO[$idioma])) {
            return;
        }
        setcookie('stockflow_lang', $idioma, [
            'expires' => time() + 31536000,
            'path' => '/',
            'samesite' => 'Lax',
        ]);
        $_COOKIE['stockflow_lang'] = $idioma;
    }

    public static function t(string $clave): string
    {
        return self::DICCIONARIO[self::actual()][$clave] ?? $clave;
    }

    public static function todo(): array
    {
        return self::DICCIONARIO[self::actual()];
    }
}
