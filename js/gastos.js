(() => {
  const tabla = document.getElementById('tabla');
  const total = document.getElementById('total');
  const form = document.getElementById('fg');
  const categoria = document.getElementById('gcat');
  const cancelar = document.getElementById('cancelar');

  const salirEdicion = () => {
    Ui.preparar(form);
    cancelar.hidden = true;
  };

  const editar = g => {
    Ui.preparar(form, { id: g.id, categoria_id: g.categoria_id, descripcion: g.descripcion, monto: g.monto, fecha: g.fecha });
    cancelar.hidden = false;
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };

  const fila = g => h('tr', {},
    h('td', { texto: g.fecha }),
    h('td', { texto: g.categoria || '-' }),
    h('td', { texto: g.descripcion }),
    h('td', { texto: dinero(g.monto) }),
    h('td', {},
      h('button', { class: 'btn sec sm', type: 'button', texto: T('edit'), onclick: () => editar(g) }),
      ' ',
      h('button', { class: 'btn dan sm', type: 'button', texto: T('delete'), onclick: () => Crud.eliminar('gastos', g.id) })));

  const cargar = () => Api.obtener('gastos.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    tabla.replaceChildren(...(data.data.length
      ? data.data.map(fila)
      : [h('tr', {}, h('td', { colspan: '5', texto: T('noData') }))]));
    total.textContent = dinero(data.data.reduce((suma, g) => suma + Number(g.monto), 0));
  });

  Crud.registrar('gastos', {
    endpoint: 'gastos.php',
    reiniciado: () => { cancelar.hidden = true; },
    despues: cargar
  });

  cancelar.addEventListener('click', salirEdicion);

  Api.obtener('categorias_gasto.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    data.data.forEach(c => categoria.append(h('option', { value: c.id, texto: c.nombre })));
  });

  cargar();
})();
