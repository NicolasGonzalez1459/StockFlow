let PRODS = [], CATS = [], EDIT = null;
async function cargar(){
  const r = await api("productos.php", "listar");
  PRODS = r.items || [];
  CATS = (await api("productos.php", "categorias")).items || [];
  cat.innerHTML = `<option value="">-</option>` + CATS.map(c => `<option value="${c.id}">${esc(c.nombre)}</option>`).join("");
  pintar();
  const al = await api("productos.php", "alertas");
  document.getElementById("alertas").innerHTML = (al.items || []).map(p =>
    `<tr><td>${esc(p.codigo)}</td><td>${esc(p.nombre)}</td><td>${p.stock}</td><td>${p.stock_minimo}</td></tr>`
  ).join("") || `<tr><td colspan="4">${t("noData")}</td></tr>`;
}
function pintar(){
  const q = (buscar.value || "").toLowerCase();
  document.getElementById("tabla").innerHTML = PRODS.filter(p => (p.nombre + p.codigo).toLowerCase().includes(q)).map(p =>
    `<tr><td>${p.imagen ? `<img class="thumb" src="../${esc(p.imagen)}">` : `<div class="thumb empty">${t("noImage") || "-"}</div>`}</td>
     <td>${esc(p.codigo)}</td><td>${esc(p.nombre)}</td><td>${esc(p.categoria || "-")}</td>
     <td>${money(p.precio_costo)}</td><td>${money(p.precio_venta)}</td>
     <td><span class="tag ${+p.stock <= +p.stock_minimo ? "low" : "ok"}">${p.stock} ${esc(p.unidad)}</span></td>
     <td>${p.stock_minimo}</td>
     <td>${USER.rol === "admin" ? `<button class="btn sec sm" onclick="abrir(${p.id})">${t("edit")}</button>
     <button class="btn dan sm" onclick="borrar(${p.id})">${t("delete")}</button>` : "-"}</td></tr>`
  ).join("") || `<tr><td colspan="9">${t("noData")}</td></tr>`;
}
function abrir(id){
  EDIT = PRODS.find(p => p.id == id) || null;
  codigo.value = EDIT ? EDIT.codigo : "";
  nombre.value = EDIT ? EDIT.nombre : "";
  cat.value = EDIT && EDIT.categoria_id ? EDIT.categoria_id : "";
  unidad.value = EDIT ? EDIT.unidad : "unidad";
  costo.value = EDIT ? EDIT.precio_costo : 0;
  precio.value = EDIT ? EDIT.precio_venta : 0;
  stock.value = EDIT ? EDIT.stock : 0;
  minimo.value = EDIT ? EDIT.stock_minimo : 5;
  stock.disabled = !!EDIT;
  imgFile.value = "";
  imgPrev.src = EDIT && EDIT.imagen ? "../" + EDIT.imagen : "";
  imgPrev.style.display = EDIT && EDIT.imagen ? "block" : "none";
  modal("mp", true);
}
async function borrar(id){
  if(!confirm("?")) return;
  await api("productos.php", "eliminar", { id });
  cargar();
}
guard("inventario", ["admin", "repositor", "vendedor"]).then(u => {
  if(!u) return;
  document.getElementById("nuevo").style.display = u.rol === "admin" ? "" : "none";
  document.getElementById("nuevo").onclick = () => abrir(0);
  buscar.oninput = pintar;
  document.getElementById("cerrar").onclick = () => modal("mp", false);
  imgFile.onchange = () => {
    if(!imgFile.files[0]) return;
    imgPrev.src = URL.createObjectURL(imgFile.files[0]);
    imgPrev.style.display = "block";
  };
  document.getElementById("fp").onsubmit = async e => {
    e.preventDefault();
    const d = { id: EDIT ? EDIT.id : 0, codigo: codigo.value, nombre: nombre.value, categoria_id: cat.value,
      unidad: unidad.value, precio_costo: costo.value, precio_venta: precio.value, stock: stock.value, stock_minimo: minimo.value };
    const r = await api("productos.php", EDIT ? "editar" : "crear", d);
    if(r.error === "codigo_duplicado"){ alert(t("dupCode")); return; }
    if(r.error){ alert(r.error); return; }
    const pid = EDIT ? EDIT.id : r.id;
    if(imgFile.files[0] && pid){
      const fd = new FormData();
      fd.append("id", pid);
      fd.append("imagen", imgFile.files[0]);
      const rimg = await fetch(API + "productos.php?a=imagen", { method: "POST", body: fd }).then(x => x.json());
      if(rimg.error) alert(rimg.error);
    }
    modal("mp", false);
    cargar();
  };
  cargar();
});
