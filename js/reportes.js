const COLS = {
  ventas:["id","fecha","cliente","canal","vendedor","total","estado"],
  stock:["codigo","nombre","stock","stock_minimo","precio_costo","precio_venta","valorizado"],
  gastos:["fecha","categoria","descripcion","monto"],
  rentabilidad:["nombre","unidades","ingresos","costos","margen"]
};
const MONEYCOLS = ["total","monto","precio_costo","precio_venta","valorizado","ingresos","costos","margen"];
const HEADER_MAP = {
  id:"#", fecha:"date", cliente:"client", canal:"channel", vendedor:"seller", total:"total", estado:"status",
  codigo:"code", nombre:"name", stock:"stock", stock_minimo:"min", precio_costo:"cost", precio_venta:"price",
  valorizado:"valorized", categoria:"category", descripcion:"description", monto:"amount",
  unidades:"units", ingresos:"revenue", costos:"costs", margen:"margin"
};
let ROWS = [];
async function cargar(){
  const tipo = rtipo.value;
  const r = await api("reportes.php", tipo, null, { desde: desde.value, hasta: hasta.value });
  ROWS = r.items || [];
  const cols = COLS[tipo];
  document.getElementById("head").innerHTML = cols.map(c => `<th>${c}</th>`).join("");
  document.getElementById("body").innerHTML = ROWS.map(x =>
    `<tr>${cols.map(c => `<td>${MONEYCOLS.includes(c) ? money(x[c]) : esc(x[c])}</td>`).join("")}</tr>`
  ).join("") || `<tr><td colspan="${cols.length}">${t("noData")}</td></tr>`;
}
guard("reportes", ["admin"]).then(u => {
  if(!u) return;
  hasta.value = new Date().toISOString().slice(0, 10);
  desde.value = "2026-01-01";
  document.getElementById("aplicar").onclick = cargar;
  rtipo.onchange = cargar;
  document.getElementById("exportar").onclick = () => {
    const cols = COLS[rtipo.value];
    const headers = cols.map(c => HEADER_MAP[c] ? t(HEADER_MAP[c]) : c);
    const filas = ROWS.map(x => cols.map(c => MONEYCOLS.includes(c) ? money(x[c]) : (x[c] ?? "-")));
    const titulo = rtipo.options[rtipo.selectedIndex].text;
    const subtitulo = t("from") + " " + desde.value + " " + t("to").toLowerCase() + " " + hasta.value;
    toReportHTML(titulo, subtitulo, headers, filas, "reporte_" + rtipo.value);
  };
  cargar();
});
