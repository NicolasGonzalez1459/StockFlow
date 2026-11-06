const Textos = (() => {
  const nodo = document.getElementById('textos');
  try { return JSON.parse(nodo ? nodo.textContent : '{}'); } catch (e) { return {}; }
})();

const Contexto = (() => {
  const nodo = document.getElementById('contexto');
  try { return JSON.parse(nodo ? nodo.textContent : '{}'); } catch (e) { return {}; }
})();

const T = clave => Textos[clave] || clave;

const dinero = n => '$' + Number(n || 0).toLocaleString('es-UY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const fechaCorta = valor => String(valor || '').replace('T', ' ').slice(0, 16);

const h = (etiqueta, atributos, ...hijos) => {
  const el = document.createElement(etiqueta);
  Object.entries(atributos || {}).forEach(([k, v]) => {
    if (v === false || v === null || v === undefined) return;
    if (k === 'texto') el.textContent = v;
    else if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
    else el.setAttribute(k, v === true ? '' : v);
  });
  hijos.flat().forEach(x => el.append(x));
  return el;
};

const Api = {
  base: 'api/',

  solicitar(metodo, ruta, datos) {
    const opciones = { method: metodo, credentials: 'same-origin', headers: { Accept: 'application/json' } };
    if (datos instanceof FormData) {
      opciones.body = datos;
    } else if (datos !== undefined) {
      opciones.headers['Content-Type'] = 'application/json';
      opciones.body = JSON.stringify(datos);
    }
    return fetch(this.base + ruta, opciones)
      .then(respuesta => respuesta.json().then(cuerpo => {
        if (respuesta.status === 401 && ruta.indexOf('sesion.php') !== 0) {
          location.href = 'index.php?p=login';
        }
        return cuerpo;
      }))
      .catch(() => {
        Ui.aviso(T('netError'), 'error');
        return { status: 'error', message: T('netError'), data: null, avisado: true };
      });
  },

  obtener(ruta) { return this.solicitar('GET', ruta); },
  crear(ruta, datos) { return this.solicitar('POST', ruta, datos); },
  actualizar(ruta, datos) { return this.solicitar('PUT', ruta, datos); },
  eliminar(ruta) { return this.solicitar('DELETE', ruta); },

  falla(data) {
    if (!data || !data.avisado) Ui.aviso(data && data.message ? data.message : T('error'), 'error');
  },

  consulta(ruta, parametros) {
    const partes = Object.entries(parametros || {})
      .filter(([, v]) => v !== '' && v !== null && v !== undefined)
      .map(([k, v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v));
    return partes.length ? ruta + '?' + partes.join('&') : ruta;
  }
};
