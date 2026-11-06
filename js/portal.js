(() => {
  const carrito = new Carrito();
  const lista = document.getElementById('carritoLista');
  const total = document.getElementById('carritoTotal');
  const contador = document.getElementById('carritoCant');
  const catalogo = document.getElementById('catalogo');
  const tabla = document.getElementById('tabla');

  const pintarCarrito = () => {
    contador.textContent = carrito.unidades();
    lista.replaceChildren(...(carrito.vacio()
      ? [h('p', { texto: T('emptyCart') })]
      : carrito.items.map(x => h('div', { class: 'cart-item' },
          h('span', { texto: x.nombre }),
          h('input', {
            type: 'number', min: '1', max: String(x.stock), value: String(x.cantidad),
            onchange: ev => { carrito.cambiar(x.producto_id, ev.target.value); pintarCarrito(); }
          }),
          h('span', { texto: dinero(x.precio * x.cantidad) }),
          h('button', { class: 'btn dan sm', type: 'button', texto: T('removeItem'), onclick: () => { carrito.quitar(x.producto_id); pintarCarrito(); } })))));
    total.textContent = dinero(carrito.total());
  };

  const tarjeta = p => {
    const sinStock = Number(p.stock) <= 0;
    const campo = h('input', { type: 'number', min: '1', max: String(p.stock), value: '1', id: 'qty-' + p.id, disabled: sinStock });
    const imagen = p.imagen ? h('img', { src: p.imagen, alt: p.nombre }) : h('div', { class: 'ph', texto: T('noImage') });
    return h('div', { class: 'prod-card' },
      h('div', { class: 'prod-img' }, imagen),
      h('div', { class: 'prod-info' },
        h('div', { class: 'prod-name', texto: p.nombre }),
        h('div', { class: 'prod-cat', texto: p.categoria || '-' }),
        h('div', { class: 'prod-price', texto: dinero(p.precio_venta) }),
        h('div', { class: 'tag ' + (sinStock ? 'low' : 'ok'), texto: sinStock ? T('outOfStock') : T('availableStock') + ': ' + p.stock }),
        h('div', { class: 'prod-buy' },
          campo,
          h('button', {
            class: 'btn sm', type: 'button', texto: T('addCart'), disabled: sinStock,
            onclick: () => {
              const cantidad = Math.max(1, Math.min(Number(p.stock), parseInt(campo.value || '1', 10) || 1));
              carrito.agregar({ producto_id: p.id, nombre: p.nombre, precio: Number(p.precio_venta), stock: Number(p.stock), cantidad });
              pintarCarrito();
              Ui.abrir('mc');
            }
          }))));
  };

  const cargarCatalogo = () => Api.obtener('productos.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    catalogo.replaceChildren(...(data.data.length ? data.data.map(tarjeta) : [h('p', { texto: T('noData') })]));
  });

  const cargarPedidos = () => Api.obtener('ventas.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    const propios = Contexto.rol === 'cliente' ? data.data : data.data.filter(v => v.cliente === Contexto.usuario.nombre);
    tabla.replaceChildren(...(propios.length
      ? propios.map(v => h('tr', {},
          h('td', { texto: '#' + v.id }),
          h('td', { texto: fechaCorta(v.fecha) }),
          h('td', { texto: v.canal }),
          h('td', { texto: dinero(v.total) }),
          h('td', {}, h('span', { class: 'tag', texto: v.estado }))))
      : [h('tr', {}, h('td', { colspan: '5', texto: T('noData') }))]));
  });

  document.getElementById('confirmarCompra').addEventListener('click', () => {
    if (carrito.vacio()) return;
    Api.crear('ventas.php', { items: carrito.detalle() }).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      Ui.aviso(T('purchaseDone'), 'ok');
      carrito.items = [];
      pintarCarrito();
      Ui.cerrar('mc');
      return Promise.all([cargarCatalogo(), cargarPedidos()]);
    });
  });

  pintarCarrito();
  cargarCatalogo();
  cargarPedidos();
})();
