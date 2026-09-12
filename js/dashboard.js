const CHART_COLORS = ["#2f7fbf","#1f8a5f","#c0392b","#1f5f92","#8b5fbf","#c98a1f"];
let DATA = null, PRODS = [];
async function cargar(){
  DATA = await api("reportes.php", "dashboard", null, { desde: desde.value, hasta: hasta.value });
  pintar();
  const n = await api("notificaciones.php", "listar");
  document.getElementById("notis").innerHTML = (n.items || []).slice(0, 6)
    .map(x => `<tr><td>${fdate(x.fecha)}</td><td>${esc(x.mensaje)}</td></tr>`).join("") || `<tr><td colspan="2">${t("noData")}</td></tr>`;
}
function donutSvg(parts, size){
  const total = parts.reduce((s, p) => s + Math.max(0, p.value), 0) || 1;
  const r = 46, c = 2 * Math.PI * r;
  let offset = 0;
  const circles = parts.map(p => {
    const frac = Math.max(0, p.value) / total;
    const len = frac * c;
    const seg = `<circle cx="60" cy="60" r="${r}" fill="none" stroke="${p.color}" stroke-width="16" stroke-dasharray="${len} ${c - len}" stroke-dashoffset="${-offset}" transform="rotate(-90 60 60)"></circle>`;
    offset += len;
    return seg;
  }).join("");
  return `<svg width="${size || 130}" height="${size || 130}" viewBox="0 0 120 120">${circles}<circle cx="60" cy="60" r="30" fill="var(--panel)"></circle></svg>`;
}
function legendHtml(parts, fmt){
  const total = parts.reduce((s, p) => s + Math.max(0, p.value), 0) || 1;
  const f = fmt || money;
  return parts.map(p => `<div class="legend-item"><span class="dot" style="background:${p.color}"></span><span class="lbl">${esc(p.label)}</span><span class="val">${f(p.value)} · ${Math.round(Math.max(0, p.value) / total * 100)}%</span></div>`).join("") || `<div class="legend-item">${t("noData")}</div>`;
}
function pintar(){
  if(!DATA) return;
  kVentas.textContent = money(DATA.ventas_total);
  kCant.textContent = DATA.ventas_cantidad;
  kGastos.textContent = money(DATA.gastos_total);
  kMargen.textContent = money(DATA.margen);
  kMargen.className = "v " + (DATA.margen >= 0 ? "ok" : "bad");
  kCritico.textContent = DATA.stock_critico;
  const m2 = Math.max(1, ...DATA.serie.map(x => +x.total));
  document.getElementById("chartSerie").innerHTML = DATA.serie.map(x =>
    `<div class="col"><span class="val">${money(x.total)}</span><span class="fill" style="height:${(x.total / m2 * 100).toFixed(0)}%"></span></div>`
  ).join("") || `<div class="col">${t("noData")}</div>`;
  document.getElementById("chartSerieLbl").innerHTML = DATA.serie.map(x => `<span>${x.dia}</span>`).join("");
  const comp = [
    { label:t("costs"), value: DATA.costo_total || 0, color: CHART_COLORS[3] },
    { label:t("expensesTotal"), value: DATA.gastos_total || 0, color: CHART_COLORS[2] },
    { label:t("margin"), value: Math.max(0, DATA.margen || 0), color: CHART_COLORS[1] }
  ];
  document.getElementById("donutComposicion").innerHTML = donutSvg(comp, 130) + `<div class="legend">${legendHtml(comp)}</div>`;
  const top = DATA.top.map((x, i) => ({ label:x.nombre, value:+x.cant || 0, color: CHART_COLORS[i % CHART_COLORS.length] }));
  document.getElementById("donutTop").innerHTML = donutSvg(top, 150) + `<div class="legend">${legendHtml(top, n => n + " u.")}</div>`;
}
async function cargarMov(){
  const r = await api("productos.php", "listar");
  PRODS = r.items || [];
  mprod.innerHTML = PRODS.map(p => `<option value="${p.id}">${esc(p.nombre)}</option>`).join("");
  const mv = await api("productos.php", "movimientos");
  document.getElementById("movs").innerHTML = (mv.items || []).slice(0, 20).map(m =>
    `<tr><td>${fdate(m.fecha)}</td><td>${esc(m.producto)}</td><td>${m.tipo}</td><td>${m.cantidad}</td><td>${m.stock_resultante}</td><td>${esc(m.usuario || "-")}</td></tr>`
  ).join("") || `<tr><td colspan="6">${t("noData")}</td></tr>`;
}
window.onLang = pintar;
guard("dashboard", ["admin", "repositor"]).then(u => {
  if(!u) return;
  const esAdmin = u.rol === "admin";
  document.getElementById("boxKpiCharts").style.display = esAdmin ? "" : "none";
  document.getElementById("boxTop").style.display = esAdmin ? "" : "none";
  document.getElementById("boxNotis").style.display = esAdmin ? "" : "none";
  document.querySelector(".panel.row").style.display = esAdmin ? "" : "none";
  document.querySelector(".cards").style.display = esAdmin ? "" : "none";
  hasta.value = new Date().toISOString().slice(0, 10);
  desde.value = "2026-01-01";
  document.getElementById("aplicar").onclick = cargar;
  if(esAdmin) cargar();
  cargarMov();
  document.getElementById("fm").onsubmit = async e => {
    e.preventDefault();
    const r = await api("productos.php", "movimiento", { producto_id: mprod.value, tipo: mtipo.value, cantidad: mcant.value, motivo: mmot.value });
    if(r.error) alert(r.error);
    mcant.value = ""; mmot.value = "";
    cargarMov();
  };
});
