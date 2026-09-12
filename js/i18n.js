const DICT = {
  es:{
    app:"StockFlow", tagline:"Sistema de Gestion de Stock y Ventas",
    login:"Iniciar sesion", email:"Correo", pass:"Contrasena", enter:"Entrar", logout:"Salir",
    dashboard:"Dashboard", inventory:"Inventario", sales:"Ventas", expenses:"Gastos",
    reports:"Reportes", users:"Usuarios", alerts:"Alertas", portal:"Mis pedidos",
    from:"Desde", to:"Hasta", apply:"Aplicar", export:"Exportar reporte", new:"Nuevo", save:"Guardar",
    cancel:"Cancelar", edit:"Editar", delete:"Eliminar", search:"Buscar",
    salesTotal:"Ventas del periodo", salesCount:"Cantidad de ventas", expensesTotal:"Gastos",
    margin:"Margen estimado", critical:"Stock critico", topProducts:"Productos mas vendidos",
    evolution:"Evolucion de ventas", composition:"Composicion de ingresos",
    code:"Codigo", name:"Nombre", category:"Categoria",
    unit:"Unidad", cost:"Precio costo", price:"Precio venta", stock:"Stock", min:"Stock minimo",
    actions:"Acciones", movements:"Movimientos", type:"Tipo", qty:"Cantidad", reason:"Motivo",
    date:"Fecha", user:"Usuario", product:"Producto", add:"Agregar", client:"Cliente",
    channel:"Canal", total:"Total", status:"Estado", seller:"Vendedor", detail:"Detalle",
    register:"Registrar venta", amount:"Monto", description:"Descripcion", role:"Rol",
    active:"Activo", income:"Ingreso", outcome:"Egreso", adjust:"Ajuste", noData:"Sin datos",
    profit:"Rentabilidad", units:"Unidades", revenue:"Ingresos", costs:"Costos", valorized:"Valorizado",
    badCreds:"Credenciales incorrectas", notifications:"Notificaciones", welcome:"Bienvenido",
    buyHere:"Poner una compra aqui", cart:"Carrito", addCart:"Agregar carrito", confirmBuy:"Confirmar compra",
    myOrders:"Mis pedidos", noImage:"Sin foto", outOfStock:"Sin stock", emptyCart:"El carrito esta vacio",
    inCart:"En el carrito", removeItem:"Quitar", availableStock:"Disponibles", photo:"Foto",
    dupCode:"Ese codigo ya esta usado por otro producto"
  },
  en:{
    app:"StockFlow", tagline:"Stock and Sales Management System",
    login:"Sign in", email:"Email", pass:"Password", enter:"Enter", logout:"Log out",
    dashboard:"Dashboard", inventory:"Inventory", sales:"Sales", expenses:"Expenses",
    reports:"Reports", users:"Users", alerts:"Alerts", portal:"My orders",
    from:"From", to:"To", apply:"Apply", export:"Export report", new:"New", save:"Save",
    cancel:"Cancel", edit:"Edit", delete:"Delete", search:"Search",
    salesTotal:"Period sales", salesCount:"Sales count", expensesTotal:"Expenses",
    margin:"Estimated margin", critical:"Critical stock", topProducts:"Top selling products",
    evolution:"Sales evolution", composition:"Revenue composition",
    code:"Code", name:"Name", category:"Category",
    unit:"Unit", cost:"Cost price", price:"Sale price", stock:"Stock", min:"Min stock",
    actions:"Actions", movements:"Movements", type:"Type", qty:"Quantity", reason:"Reason",
    date:"Date", user:"User", product:"Product", add:"Add", client:"Client",
    channel:"Channel", total:"Total", status:"Status", seller:"Seller", detail:"Detail",
    register:"Register sale", amount:"Amount", description:"Description", role:"Role",
    active:"Active", income:"Stock in", outcome:"Stock out", adjust:"Adjust", noData:"No data",
    profit:"Profitability", units:"Units", revenue:"Revenue", costs:"Costs", valorized:"Valued stock",
    badCreds:"Invalid credentials", notifications:"Notifications", welcome:"Welcome",
    buyHere:"Place an order here", cart:"Cart", addCart:"Add to cart", confirmBuy:"Confirm purchase",
    myOrders:"My orders", noImage:"No photo", outOfStock:"Out of stock", emptyCart:"Your cart is empty",
    inCart:"In cart", removeItem:"Remove", availableStock:"Available", photo:"Photo",
    dupCode:"That code is already used by another product"
  }
};
let LANG = localStorage.getItem("lang") || "es";
const t = k => (DICT[LANG][k] || k);
function setLang(l){ LANG = l; localStorage.setItem("lang", l); applyI18n(); }
function applyI18n(){
  document.querySelectorAll("[data-i18n]").forEach(e => e.textContent = t(e.dataset.i18n));
  document.querySelectorAll("[data-i18n-ph]").forEach(e => e.placeholder = t(e.dataset.i18nPh));
  const s = document.getElementById("lang");
  if(s) s.value = LANG;
  if(window.onLang) window.onLang();
}
