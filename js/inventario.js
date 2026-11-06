(() => {
  const esAdmin = Contexto.rol === 'admin';
  const tabla = document.getElementById('tabla');
  const alertas = document.getElementById('alertas');
  const buscar = document.getElementById('buscar');
  const categoria = document.getElementById('cat');
  let productos = [];

  const normalizar = texto => String(texto || '').toLowerCase();

  const fila = p => {
    const bajo = Number(p.stock) <= Number(p.stock_minimo);
    const imagen = p.imagen
      ? h('img', { class: 'thumb', src: p.imagen, alt: '' })
      : h('div', { class: 'thumb empty', texto: T('noImage') });
    const acciones = esAdmin
      ? h('td', {},
          h('button', { class: 'btn sec sm', type: 'button', texto: T('edit'), onclick: () => Ui.abrir('mp', p) }),
          ' ',
          h('button', { class: 'btn dan sm', type: 'button', texto: T('delete'), onclick: () => Crud.eliminar('productos', p.id) }))
      : h('td', { texto: '-' });
    return h('tr', {},
      h('td', {}, imagen),
      h('td', { texto: p.codigo }),
      h('td', { texto: p.nombre }),
      h('td', { texto: p.categoria || '-' }),
      h('td', { texto: dinero(p.precio_costo) }),
      h('td', { texto: dinero(p.precio_venta) }),
      h('td', {}, h('span', { class: 'tag ' + (bajo ? 'low' : 'ok'), texto: p.stock + ' ' + p.unidad })),
      h('td', { texto: p.stock_minimo }),
      acciones);
  };

  const pintar = () => {
    const q = normalizar(buscar.value.trim());
    const visibles = productos.filter(p => q === '' || normalizar(p.nombre + p.codigo).indexOf(q) !== -1);
    tabla.replaceChildren(...(visibles.length
      ? visibles.map(fila)
      : [h('tr', {}, h('td', { colspan: '9', texto: T('noData') }))]));
  };

  const pintarAlertas = lista => {
    alertas.replaceChildren(...(lista.length
      ? lista.map(a => h('tr', {},
          h('td', { texto: a.codigo }),
          h('td', { texto: a.nombre }),
          h('td', { texto: a.stock }),
          h('td', { texto: a.stock_minimo })))
      : [h('tr', {}, h('td', { colspan: '4', texto: T('noData') }))]));
  };

  const cargar = () => Promise.all([
    Api.obtener('productos.php'),
    Api.obtener('productos.php?stock_bajo=1')
  ]).then(([lista, bajos]) => {
    if (lista.status !== 'success') {
      Api.falla(lista);
      return;
    }
    productos = lista.data;
    pintar();
    if (bajos.status === 'success') pintarAlertas(bajos.data);
  });

  document.getElementById('fbuscar').addEventListener('submit', e => {
    e.preventDefault();
    pintar();
  });
  buscar.addEventListener('input', pintar);

  if (esAdmin) {
    Api.obtener('categorias_producto.php').then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      data.data.forEach(c => categoria.append(h('option', { value: c.id, texto: c.nombre })));
    });

    Crud.registrar('productos', {
      endpoint: 'productos.php',
      posterior: (form, respuesta) => {
        const archivo = form.querySelector('input[type=file]');
        if (!archivo || !archivo.files[0]) return null;
        const envio = new FormData();
        envio.append('producto_id', respuesta.data.id);
        envio.append('imagen', archivo.files[0]);
        return Api.crear('imagenes.php', envio);
      },
      despues: cargar
    });
  }

  cargar();
})();
