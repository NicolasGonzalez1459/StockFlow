let PRODS = [], CAR = [];
async function cargar(){
  PRODS = (await api("productos.php", "listar")).items || [];
  vprod.innerHTML = PRODS.map(p => `<option value="${p.id}">${esc(p.nombre)} (${p.stock})</option>`).join("");
  const r = await api("ventas.php", "listar");
  document.getElementById("tabla").innerHTML = (r.items || []).map(v =>
    `<tr><td>#${v.id}</td><td>${fdate(v.fecha)}</td><td>${esc(v.cliente || "-")}</td><td>${esc(v.canal)}</td>
     <td>${esc(v.vendedor || "-")}</td><td>${money(v.total)}</td><td><span class="tag">${v.estado}</span></td>
     <td><button class="btn sec sm" onclick="ver(${v.id})">${t("detail")}</button></td></tr>`
  ).join("") || `<tr><td colspan="8">${t("noData")}</td></tr>`;
}
async function ver(id){
  const r = await api("ventas.php", "detalle", null, { id });
  document.getElementById("det").innerHTML = (r.items || []).map(d =>
    `<tr><td>${esc(d.producto || "-")}</td><td>${d.cantidad}</td><td>${money(d.precio_unitario)}</td><td>${money(d.subtotal)}</td></tr>`
  ).join("");
  modal("md", true);
}
function pintarCar(){
  document.getElementById("car").innerHTML = CAR.map((c, i) =>
    `<tr><td>${esc(c.nombre)}</td><td>${c.cantidad}</td><td>${money(c.precio * c.cantidad)}</td>
     <td><button class="btn dan sm" onclick="quitar(${i})">x</button></td></tr>`
  ).join("") || `<tr><td colspan="4">${t("noData")}</td></tr>`;
  document.getElementById("tot").textContent = money(CAR.reduce((a, c) => a + c.precio * c.cantidad, 0));
}
function quitar(i){ CAR.splice(i, 1); pintarCar(); }
guard("ventas", ["admin", "vendedor"]).then(u => {
  if(!u) return;
  document.getElementById("agregar").onclick = () => {
    const p = PRODS.find(x => x.id == vprod.value);
    const c = parseInt(vcant.value || "0");
    if(!p || c < 1) return;
    CAR.push({ producto_id: p.id, nombre: p.nombre, precio: +p.precio_venta, cantidad: c });
    vcant.value = 1;
    pintarCar();
  };
  document.getElementById("guardar").onclick = async () => {
    if(!CAR.length) return;
    const r = await api("ventas.php", "crear", { cliente: vcli.value, canal: vcanal.value, items: CAR });
    if(r.error) return alert(r.error);
    CAR = []; vcli.value = "";
    pintarCar();
    cargar();
  };
  document.getElementById("cerrar").onclick = () => modal("md", false);
  pintarCar();
  cargar();
});
