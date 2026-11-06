(() => {
  const COLUMNAS = {
    ventas: ['id', 'fecha', 'cliente', 'canal', 'vendedor', 'total', 'estado'],
    stock: ['codigo', 'nombre', 'stock', 'stock_minimo', 'precio_costo', 'precio_venta', 'valorizado'],
    gastos: ['fecha', 'categoria', 'descripcion', 'monto'],
    rentabilidad: ['nombre', 'unidades', 'ingresos', 'costos', 'margen']
  };
  const DINERO = ['total', 'monto', 'precio_costo', 'precio_venta', 'valorizado', 'ingresos', 'costos', 'margen'];
  const ETIQUETAS = {
    id: '#', fecha: 'date', cliente: 'client', canal: 'channel', vendedor: 'seller', total: 'total', estado: 'status',
    codigo: 'code', nombre: 'name', stock: 'stock', stock_minimo: 'min', precio_costo: 'cost', precio_venta: 'price',
    valorizado: 'valorized', categoria: 'category', descripcion: 'description', monto: 'amount',
    unidades: 'units', ingresos: 'revenue', costos: 'costs', margen: 'margin'
  };
  const TIPOS = { ventas: 'repSales', stock: 'repStock', gastos: 'repExpenses', rentabilidad: 'repProfit' };

  const form = document.getElementById('frep');
  const cabecera = document.getElementById('head');
  const cuerpo = document.getElementById('body');
  let filas = [];
  let tipo = 'ventas';
  let desde = '';
  let hasta = '';

  const encabezado = columna => ETIQUETAS[columna] === '#' ? '#' : T(ETIQUETAS[columna] || columna);

  const celda = (fila, columna) => {
    const valor = fila[columna];
    if (valor === null || valor === undefined || valor === '') return '-';
    return DINERO.indexOf(columna) !== -1 ? dinero(valor) : String(valor);
  };

  const pintar = () => {
    const columnas = COLUMNAS[tipo];
    cabecera.replaceChildren(...columnas.map(c => h('th', { texto: encabezado(c) })));
    cuerpo.replaceChildren(...(filas.length
      ? filas.map(f => h('tr', {}, columnas.map(c => h('td', { texto: celda(f, c) }))))
      : [h('tr', {}, h('td', { colspan: String(columnas.length), texto: T('noData') }))]));
  };

  const cargar = () => {
    tipo = form.elements.tipo.value;
    desde = form.elements.desde.value;
    hasta = form.elements.hasta.value;
    return Api.obtener(Api.consulta('reportes.php', { tipo, desde, hasta })).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      filas = data.data;
      pintar();
    });
  };

  const escapar = valor => String(valor).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');

  const documento = () => {
    const columnas = COLUMNAS[tipo];
    const titulo = T(TIPOS[tipo]);
    const subtitulo = T('from') + ' ' + desde + ' ' + T('to').toLowerCase() + ' ' + hasta;
    const generado = new Date().toLocaleString('es-UY');
    const encabezados = columnas.map(c => '<th>' + escapar(encabezado(c)) + '</th>').join('');
    const lineas = filas.length
      ? filas.map(f => '<tr>' + columnas.map(c => '<td>' + escapar(celda(f, c)) + '</td>').join('') + '</tr>').join('')
      : '<tr><td colspan="' + columnas.length + '" class="vacio">' + escapar(T('noData')) + '</td></tr>';
    return '<!DOCTYPE html><html lang="' + escapar(Contexto.idioma || 'es') + '"><head><meta charset="utf-8"><title>' + escapar(titulo) + ' | StockFlow</title><style>'
      + ':root{--acc:#2f7fbf;--acc-dark:#1f5f92;--line:#dde6ef;--muted:#64748b;--txt:#223047;--panel2:#f3f7fb}'
      + '*{box-sizing:border-box;margin:0;padding:0}'
      + 'body{font-family:Georgia,"Times New Roman",serif;color:var(--txt);background:#ffffff;padding:36px}'
      + '.enc{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid var(--acc);padding-bottom:14px;margin-bottom:22px}'
      + '.enc h1{font-size:22px;color:var(--acc-dark);font-weight:700;letter-spacing:.3px}'
      + '.enc .marca{font-size:13px;color:var(--muted);margin-top:4px}'
      + '.meta{text-align:right;font-size:12px;color:var(--muted);line-height:1.6}'
      + 'h2{font-size:15px;font-weight:700;margin-bottom:14px;color:var(--txt)}'
      + 'table{width:100%;border-collapse:collapse;font-size:12px}'
      + 'th,td{text-align:left;padding:8px 10px;border-bottom:1px solid var(--line)}'
      + 'th{background:var(--acc);color:#ffffff;font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:.4px}'
      + 'tbody tr:nth-child(even){background:var(--panel2)}'
      + '.vacio{text-align:center;color:var(--muted);padding:20px}'
      + '.pie{margin-top:30px;border-top:1px solid var(--line);padding-top:12px;font-size:10px;color:var(--muted);display:flex;justify-content:space-between}'
      + '@media print{body{padding:0}}'
      + '</style></head><body>'
      + '<div class="enc"><div><h1>StockFlow</h1><div class="marca">' + escapar(Contexto.empresa || '') + '</div></div>'
      + '<div class="meta">' + escapar(subtitulo) + '<br>' + escapar(T('generated')) + ' ' + escapar(generado) + '</div></div>'
      + '<h2>' + escapar(titulo) + '</h2><table><thead><tr>' + encabezados + '</tr></thead><tbody>' + lineas + '</tbody></table>'
      + '<div class="pie"><span>POLO EDUCATIVO TECNOLOGICO — CETP-UTU</span><span>Proyecto de Egreso BT Informatica 2026</span></div>'
      + '</body></html>';
  };

  form.addEventListener('submit', e => {
    e.preventDefault();
    cargar();
  });

  document.getElementById('exportar').addEventListener('click', () => {
    const enlace = h('a', {
      href: URL.createObjectURL(new Blob([documento()], { type: 'text/html;charset=utf-8' })),
      download: 'reporte_' + tipo + '.html'
    });
    document.body.append(enlace);
    enlace.click();
    enlace.remove();
  });

  cargar();
})();
