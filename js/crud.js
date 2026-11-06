const Crud = {
  recursos: {},

  registrar(nombre, config) {
    this.recursos[nombre] = config;
  },

  datos(form) {
    const datos = {};
    new FormData(form).forEach((valor, clave) => {
      if (typeof valor !== 'string') return;
      const campo = form.elements[clave];
      datos[clave] = campo && campo.type === 'number' && valor !== '' ? Number(valor) : valor;
    });
    return datos;
  },

  enviar(form) {
    const config = this.recursos[form.dataset.recurso];
    if (!config) return Promise.resolve();
    const id = form.dataset.id || '';
    const datos = this.datos(form);
    const solicitud = id
      ? Api.actualizar(config.endpoint + '?id=' + encodeURIComponent(id), datos)
      : Api.crear(config.endpoint, datos);
    return solicitud.then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return null;
      }
      return Promise.resolve(config.posterior ? config.posterior(form, data) : null).then(extra => {
        if (extra && extra.status !== 'success') Api.falla(extra);
        Ui.aviso(data.message, 'ok');
        const modal = form.closest('.modal');
        if (modal) Ui.cerrar(modal.id);
        Ui.preparar(form);
        if (config.reiniciado) config.reiniciado(form);
        return config.despues ? config.despues(data) : null;
      });
    });
  },

  eliminar(nombre, id) {
    const config = this.recursos[nombre];
    if (!config || !confirm(T('confirmDelete'))) return Promise.resolve();
    return Api.eliminar(config.endpoint + '?id=' + encodeURIComponent(id)).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return null;
      }
      Ui.aviso(data.message, 'ok');
      return config.despues ? config.despues(data) : null;
    });
  }
};

document.addEventListener('submit', e => {
  const form = e.target.closest('form[data-recurso]');
  if (!form) return;
  e.preventDefault();
  Crud.enviar(form);
});
