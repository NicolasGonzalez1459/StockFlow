(() => {
  const tabla = document.getElementById('tabla');

  const fila = u => h('tr', {},
    h('td', { texto: u.id }),
    h('td', { texto: u.nombre }),
    h('td', { texto: u.email }),
    h('td', {}, h('span', { class: 'tag', texto: u.rol })),
    h('td', { texto: Number(u.activo) ? T('yes') : T('no') }),
    h('td', {},
      h('button', { class: 'btn sec sm', type: 'button', texto: T('edit'), onclick: () => Ui.abrir('mu', { id: u.id, nombre: u.nombre, email: u.email, rol: u.rol, activo: Number(u.activo) }) }),
      ' ',
      h('button', { class: 'btn dan sm', type: 'button', texto: T('delete'), onclick: () => Crud.eliminar('usuarios', u.id) })));

  const cargar = () => Api.obtener('usuarios.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    tabla.replaceChildren(...(data.data.length
      ? data.data.map(fila)
      : [h('tr', {}, h('td', { colspan: '6', texto: T('noData') }))]));
  });

  Crud.registrar('usuarios', { endpoint: 'usuarios.php', despues: cargar });

  cargar();
})();
