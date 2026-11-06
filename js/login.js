(() => {
  const form = document.getElementById('form');
  const mensaje = document.getElementById('msg');
  const boton = document.getElementById('entrar');

  form.addEventListener('submit', e => {
    e.preventDefault();
    mensaje.textContent = '';
    boton.disabled = true;
    Api.crear('sesion.php', {
      email: document.getElementById('email').value,
      password: document.getElementById('pass').value
    }).then(data => {
      if (data.status !== 'success') {
        mensaje.textContent = data.message;
        boton.disabled = false;
        return;
      }
      location.href = 'index.php?p=' + data.data.inicio;
    });
  });
})();
