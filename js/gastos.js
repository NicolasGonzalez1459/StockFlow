async function cargar(){
  const c = await api("gastos.php", "categorias");
  gcat.innerHTML = (c.items || []).map(x => `<option value="${x.id}">${esc(x.nombre)}</option>`).join("");
  const r = await api("gastos.php", "listar");
  const items = r.items || [];
  document.getElementById("tabla").innerHTML = items.map(g =>
    `<tr><td>${g.fecha}</td><td>${esc(g.categoria || "-")}</td><td>${esc(g.descripcion)}</td>
     <td>${money(g.monto)}</td><td><button class="btn dan sm" onclick="borrar(${g.id})">${t("delete")}</button></td></tr>`
  ).join("") || `<tr><td colspan="5">${t("noData")}</td></tr>`;
  document.getElementById("total").textContent = money(items.reduce((a, g) => a + +g.monto, 0));
}
async function borrar(id){
  await api("gastos.php", "eliminar", { id });
  cargar();
}
guard("gastos", ["admin"]).then(u => {
  if(!u) return;
  gfec.value = new Date().toISOString().slice(0, 10);
  document.getElementById("fg").onsubmit = async e => {
    e.preventDefault();
    await api("gastos.php", "crear", { categoria_id: gcat.value, descripcion: gdesc.value, monto: gmonto.value, fecha: gfec.value });
    gdesc.value = ""; gmonto.value = "";
    cargar();
  };
  cargar();
});
