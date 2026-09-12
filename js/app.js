const API = "../php/";
async function api(file, action, data, params){
  let url = API + file + "?a=" + action;
  if(params) url += "&" + new URLSearchParams(params);
  const opt = { headers:{ "Content-Type":"application/json" } };
  if(data){ opt.method = "POST"; opt.body = JSON.stringify(data); }
  const r = await fetch(url, opt);
  const j = await r.json().catch(() => ({ error:"respuesta" }));
  if(r.status === 401){ location.href = "index.html"; throw new Error("auth"); }
  return j;
}
const money = n => "$" + Number(n || 0).toLocaleString("es-UY", { minimumFractionDigits:2, maximumFractionDigits:2 });
const fdate = d => (d || "").toString().replace("T", " ").slice(0, 16);
const esc = s => String(s ?? "").replace(/[<>&]/g, c => ({ "<":"&lt;", ">":"&gt;", "&":"&amp;" }[c]));
const MENU = [
  { id:"dashboard", file:"dashboard.html", key:"dashboard", roles:["admin","repositor"] },
  { id:"inventario", file:"inventario.html", key:"inventory", roles:["admin","repositor","vendedor"] },
  { id:"ventas", file:"ventas.html", key:"sales", roles:["admin","vendedor"] },
  { id:"gastos", file:"gastos.html", key:"expenses", roles:["admin"] },
  { id:"reportes", file:"reportes.html", key:"reports", roles:["admin"] },
  { id:"usuarios", file:"usuarios.html", key:"users", roles:["admin"] },
  { id:"portal", file:"portal.html", key:"portal", roles:["cliente","admin"] }
];
let USER = null;
async function guard(page, roles){
  const r = await api("auth.php", "sesion");
  USER = r.usuario;
  if(!USER){ location.href = "index.html"; return null; }
  if(roles && !roles.includes(USER.rol)){ location.href = homeOf(USER.rol); return null; }
  render(page);
  return USER;
}
function homeOf(rol){
  if(rol === "admin") return "dashboard.html";
  if(rol === "vendedor") return "ventas.html";
  if(rol === "repositor") return "inventario.html";
  return "portal.html";
}
function render(page){
  const nav = document.getElementById("nav");
  nav.innerHTML = MENU.filter(m => m.roles.includes(USER.rol))
    .map(m => `<a href="${m.file}" class="${m.id === page ? "on" : ""}" data-i18n="${m.key}">${t(m.key)}</a>`).join("");
  document.getElementById("who").textContent = USER.nombre + " · " + USER.rol;
  document.getElementById("lang").onchange = e => setLang(e.target.value);
  document.getElementById("salir").onclick = async () => { await api("auth.php", "logout"); location.href = "index.html"; };
  applyI18n();
}
function toReportHTML(titulo, subtitulo, headers, filas, name){
  const filaHtml = r => `<tr>${r.map(v => `<td>${esc(v)}</td>`).join("")}</tr>`;
  const cuerpo = filas.length ? filas.map(filaHtml).join("") : `<tr><td colspan="${headers.length}" class="vacio">${t("noData")}</td></tr>`;
  const gen = new Date().toLocaleString("es-UY");
  const html = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>${esc(titulo)} | StockFlow</title>
<style>
:root{--acc:#2f7fbf;--acc-dark:#1f5f92;--line:#dde6ef;--muted:#64748b;--txt:#223047;--panel2:#f3f7fb}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Georgia,"Times New Roman",serif;color:var(--txt);background:#ffffff;padding:36px}
.enc{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid var(--acc);padding-bottom:14px;margin-bottom:22px}
.enc h1{font-size:22px;color:var(--acc-dark);font-weight:700;letter-spacing:.3px}
.enc .marca{font-size:13px;color:var(--muted);margin-top:4px}
.meta{text-align:right;font-size:12px;color:var(--muted);line-height:1.6}
h2{font-size:15px;font-weight:700;margin-bottom:14px;color:var(--txt)}
table{width:100%;border-collapse:collapse;font-size:12px}
th,td{text-align:left;padding:8px 10px;border-bottom:1px solid var(--line)}
th{background:var(--acc);color:#ffffff;font-weight:700;text-transform:uppercase;font-size:10px;letter-spacing:.4px}
tbody tr:nth-child(even){background:var(--panel2)}
.vacio{text-align:center;color:var(--muted);padding:20px}
.pie{margin-top:30px;border-top:1px solid var(--line);padding-top:12px;font-size:10px;color:var(--muted);display:flex;justify-content:space-between}
@media print{body{padding:0}}
</style>
</head>
<body>
<div class="enc">
  <div><h1>StockFlow</h1><div class="marca">HuskyTech Solutions</div></div>
  <div class="meta">${esc(subtitulo)}<br>Generado el ${esc(gen)}</div>
</div>
<h2>${esc(titulo)}</h2>
<table>
<thead><tr>${headers.map(h => `<th>${esc(h)}</th>`).join("")}</tr></thead>
<tbody>${cuerpo}</tbody>
</table>
<div class="pie"><span>POLO EDUCATIVO TECNOLOGICO — CETP-UTU</span><span>Proyecto de Egreso BT Informatica 2026</span></div>
</body>
</html>`;
  const a = document.createElement("a");
  a.href = URL.createObjectURL(new Blob([html], { type:"text/html" }));
  a.download = name + ".html";
  a.click();
}
function modal(id, on){ document.getElementById(id).classList.toggle("on", on); }
