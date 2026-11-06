(() => {
  const carrito = new Carrito();
  const cuerpo = document.getElementById('car');
  const total = document.getElementById('tot');
  const select = document.getElementById('vprod');
  const cantidad = document.getElementById('vcant');
  const tabla = document.getElementById('tabla');
  const detalle = document.getElementById('det');
  const estados = ['pendiente', 'enviado', 'entregado', 'cancelado'];

  const pintarCarrito = () => {
    cuerpo.replaceChildren(...(carrito.vacio()
      ? [h('tr', {}, h('td', { colspan: '4', texto: T('noData') }))]
      : carrito.items.map(x => h('tr', {},
          h('td', { texto: x.nombre }),
          h('td', { texto: x.cantidad }),
          h('td', { texto: dinero(x.precio * x.cantidad) }),
          h('td', {}, h('button', { class: 'btn dan sm', type: 'button', texto: 'x', onclick: () => { carrito.quitar(x.producto_id); pintarCarrito(); } }))))));
    total.textContent = dinero(carrito.total());
  };

  const cargarProductos = () => Api.obtener('productos.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    select.replaceChildren(...data.data.map(p => h('option', {
      value: p.id,
      'data-nombre': p.nombre,
      'data-precio': p.precio_venta,
      'data-stock': p.stock,
      disabled: Number(p.stock) <= 0,
      texto: p.nombre + ' (' + p.stock + ')'
    })));
  });

  const cambiarEstado = (venta, estado, selector) => {
    Api.actualizar('ventas.php?id=' + venta.id, { estado }).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        selector.value = venta.estado;
        return;
      }
      venta.estado = data.data.estado;
      Ui.aviso(data.message, 'ok');
    });
  };

  const fila = v => {
    const selector = h('select', { class: 'estado' }, estados.map(e => h('option', { value: e, texto: e, selected: e === v.estado })));
    selector.addEventListener('change', () => cambiarEstado(v, selector.value, selector));
    return h('tr', {},
      h('td', { texto: '#' + v.id }),
      h('td', { texto: fechaCorta(v.fecha) }),
      h('td', { texto: v.cliente || '-' }),
      h('td', { texto: v.canal }),
      h('td', { texto: v.vendedor || '-' }),
      h('td', { texto: dinero(v.total) }),
      h('td', {}, selector),
      h('td', {}, h('button', { class: 'btn sec sm', type: 'button', texto: T('detail'), onclick: () => verDetalle(v.id) })));
  };

  const cargarVentas = () => Api.obtener('ventas.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    tabla.replaceChildren(...(data.data.length
      ? data.data.map(fila)
      : [h('tr', {}, h('td', { colspan: '8', texto: T('noData') }))]));
  });

  const verDetalle = id => Api.obtener('ventas.php?id=' + id).then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    detalle.replaceChildren(...data.data.items.map(d => h('tr', {},
      h('td', { texto: d.producto || '-' }),
      h('td', { texto: d.cantidad }),
      h('td', { texto: dinero(d.precio_unitario) }),
      h('td', { texto: dinero(d.subtotal) }))));
    Ui.abrir('md');
  });

  document.getElementById('agregar').addEventListener('click', () => {
    const opcion = select.options[select.selectedIndex];
    const cant = parseInt(cantidad.value || '0', 10);
    if (!opcion || cant < 1) return;
    carrito.agregar({
      producto_id: parseInt(opcion.value, 10),
      nombre: opcion.dataset.nombre,
      precio: parseFloat(opcion.dataset.precio),
      stock: parseInt(opcion.dataset.stock, 10),
      cantidad: cant
    });
    cantidad.value = 1;
    pintarCarrito();
  });

  document.getElementById('guardar').addEventListener('click', () => {
    if (carrito.vacio()) return;
    Api.crear('ventas.php', {
      cliente: document.getElementById('vcli').value,
      canal: document.getElementById('vcanal').value,
      items: carrito.detalle()
    }).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      Ui.aviso(T('saleDone'), 'ok');
      carrito.items = [];
      document.getElementById('vcli').value = '';
      pintarCarrito();
      return Promise.all([cargarVentas(), cargarProductos()]);
    });
  });

  pintarCarrito();
  cargarProductos();
  cargarVentas();
})();
