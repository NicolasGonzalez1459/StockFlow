let EDIT = null;
async function cargar(){
  const r = await api("usuarios.php", "listar");
  document.getElementById("tabla").innerHTML = (r.items || []).map(u =>
    `<tr><td>${u.id}</td><td>${esc(u.nombre)}</td><td>${esc(u.email)}</td><td><span class="tag">${u.rol}</span></td>
     <td>${+u.activo ? "Si" : "No"}</td>
     <td><button class="btn sec sm" onclick='abrir(${JSON.stringify(u)})'>${t("edit")}</button>
     <button class="btn dan sm" onclick="borrar(${u.id})">${t("delete")}</button></td></tr>`
  ).join("");
}
function abrir(u){
  EDIT = u || null;
  unombre.value = u ? u.nombre : "";
  uemail.value = u ? u.email : "";
  urol.value = u ? u.rol : "vendedor";
  uact.value = u ? u.activo : 1;
  upass.value = "";
  modal("mu", true);
}
async function borrar(id){
  await api("usuarios.php", "eliminar", { id });
  cargar();
}
guard("usuarios", ["admin"]).then(u => {
  if(!u) return;
  document.getElementById("nuevo").onclick = () => abrir(null);
  document.getElementById("cerrar").onclick = () => modal("mu", false);
  document.getElementById("fu").onsubmit = async e => {
    e.preventDefault();
    const d = { id: EDIT ? EDIT.id : 0, nombre: unombre.value, email: uemail.value, rol: urol.value, activo: uact.value, password: upass.value };
    await api("usuarios.php", EDIT ? "editar" : "crear", d);
    modal("mu", false);
    cargar();
  };
  cargar();
});
