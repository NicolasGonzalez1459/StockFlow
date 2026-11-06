const Ui = {
  abrir(id, datos) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const form = modal.querySelector('form');
    if (form) this.preparar(form, datos);
    modal.classList.add('on');
  },

  cerrar(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('on');
  },

  preparar(form, datos) {
    form.reset();
    delete form.dataset.id;
    const soloCrear = (form.dataset.soloCrear || '').split(',').filter(Boolean);
    form.querySelectorAll('[name]').forEach(campo => {
      if (datos && campo.name in datos && campo.type !== 'file') {
        campo.value = datos[campo.name] === null ? '' : datos[campo.name];
      }
      if (soloCrear.includes(campo.name)) campo.disabled = !!datos;
    });
    if (datos && datos.id) form.dataset.id = datos.id;
    form.querySelectorAll('[data-src-campo]').forEach(img => {
      const ruta = datos ? datos[img.dataset.srcCampo] : '';
      img.src = ruta || '';
      img.style.display = ruta ? 'block' : 'none';
    });
  },

  aviso(texto, tipo) {
    const contenedor = document.getElementById('avisos');
    if (!contenedor) return;
    const nodo = h('div', { class: 'aviso ' + (tipo === 'error' ? 'error' : 'ok'), role: 'status', texto });
    contenedor.append(nodo);
    setTimeout(() => nodo.remove(), 4500);
  }
};

document.addEventListener('click', e => {
  const salir = e.target.closest('[data-salir]');
  if (salir) {
    Api.eliminar('sesion.php').then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      location.href = 'index.php?p=login';
    });
    return;
  }
  const menu = e.target.closest('[data-menu]');
  if (menu) {
    const layout = document.getElementById('layout');
    if (layout) {
      const abierto = layout.classList.toggle('menu-open');
      document.querySelectorAll('[data-menu][aria-expanded]').forEach(boton => boton.setAttribute('aria-expanded', abierto ? 'true' : 'false'));
    }
    return;
  }
  const abrir = e.target.closest('[data-abrir]');
  if (abrir) {
    Ui.abrir(abrir.dataset.abrir);
    return;
  }
  const cerrar = e.target.closest('[data-cerrar]');
  if (cerrar) {
    Ui.cerrar(cerrar.dataset.cerrar);
    return;
  }
  if (e.target.classList && e.target.classList.contains('modal')) {
    e.target.classList.remove('on');
  }
});

window.addEventListener('resize', () => {
  if (window.innerWidth > 860) return;
  const layout = document.getElementById('layout');
  if (layout) layout.classList.remove('menu-open');
});

document.addEventListener('change', e => {
  const idioma = e.target.closest('[data-idioma]');
  if (idioma) {
    const url = new URL(location.href);
    url.searchParams.set('lang', idioma.value);
    location.href = url.toString();
    return;
  }
  const archivo = e.target.closest('input[type=file][data-preview]');
  if (archivo && archivo.files[0]) {
    const img = document.getElementById(archivo.dataset.preview);
    if (img) {
      img.src = URL.createObjectURL(archivo.files[0]);
      img.style.display = 'block';
    }
  }
});
