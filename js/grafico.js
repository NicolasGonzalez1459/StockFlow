const Grafico = {
  colores: ['#2f7fbf', '#1f8a5f', '#c0392b', '#1f5f92', '#8b5fbf', '#c98a1f'],

  total(partes) {
    return partes.reduce((suma, p) => suma + Math.max(0, p.valor), 0) || 1;
  },

  dona(partes, tam) {
    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('width', tam);
    svg.setAttribute('height', tam);
    svg.setAttribute('viewBox', '0 0 120 120');
    const total = this.total(partes);
    const r = 46;
    const c = 2 * Math.PI * r;
    let desplazamiento = 0;
    partes.forEach(p => {
      const largo = Math.max(0, p.valor) / total * c;
      const arco = document.createElementNS(ns, 'circle');
      arco.setAttribute('cx', 60);
      arco.setAttribute('cy', 60);
      arco.setAttribute('r', r);
      arco.setAttribute('fill', 'none');
      arco.setAttribute('stroke', p.color);
      arco.setAttribute('stroke-width', 16);
      arco.setAttribute('stroke-dasharray', largo.toFixed(3) + ' ' + (c - largo).toFixed(3));
      arco.setAttribute('stroke-dashoffset', (-desplazamiento).toFixed(3));
      arco.setAttribute('transform', 'rotate(-90 60 60)');
      svg.append(arco);
      desplazamiento += largo;
    });
    const centro = document.createElementNS(ns, 'circle');
    centro.setAttribute('cx', 60);
    centro.setAttribute('cy', 60);
    centro.setAttribute('r', 30);
    centro.setAttribute('fill', 'var(--panel)');
    svg.append(centro);
    return svg;
  },

  leyenda(partes, formato) {
    const total = this.total(partes);
    const caja = h('div', { class: 'legend' });
    if (!partes.length) {
      caja.append(h('div', { class: 'legend-item', texto: T('noData') }));
      return caja;
    }
    partes.forEach(p => {
      const pct = Math.round(Math.max(0, p.valor) / total * 100);
      caja.append(h('div', { class: 'legend-item' },
        h('span', { class: 'dot', style: 'background:' + p.color }),
        h('span', { class: 'lbl', texto: p.etiqueta }),
        h('span', { class: 'val', texto: formato(p.valor) + ' · ' + pct + '%' })));
    });
    return caja;
  },

  dibujarDona(contenedor, partes, tam, formato) {
    contenedor.replaceChildren(this.dona(partes, tam), this.leyenda(partes, formato));
  },

  barras(contenedor, etiquetas, serie) {
    if (!serie.length) {
      contenedor.replaceChildren(h('div', { class: 'col', texto: T('noData') }));
      etiquetas.replaceChildren();
      return;
    }
    const max = serie.reduce((m, x) => Math.max(m, Number(x.total)), 1);
    contenedor.replaceChildren(...serie.map(x => h('div', { class: 'col' },
      h('span', { class: 'val', texto: dinero(x.total) }),
      h('span', { class: 'fill', style: 'height:' + Math.round(Number(x.total) / max * 100) + '%' }))));
    etiquetas.replaceChildren(...serie.map(x => h('span', { texto: x.dia })));
  }
};
