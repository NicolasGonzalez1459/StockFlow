applyI18n();

document.querySelectorAll('.lang-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    setLang(e.target.getAttribute('data-lang'));
  });
});

document.getElementById("form").onsubmit = async e => {
  e.preventDefault();
  const r = await fetch("../php/auth.php?a=login", {
    method:"POST",
    headers:{ "Content-Type":"application/json" },
    body: JSON.stringify({ email: email.value, password: pass.value })
  }).then(x => x.json());
  if(r.usuario) location.href = homeOf(r.usuario.rol);
  else document.getElementById("msg").textContent = t("badCreds");
};
