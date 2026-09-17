<?php
require __DIR__ . '/config/config.php';

if (Sesion::usuarioActual()) {
    header('Location: ' . Sesion::inicioDe(Sesion::usuarioActual()['rol']));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $usuario = Usuario::autenticar($email, $password);
    if ($usuario) {
        Sesion::iniciarSesionUsuario($usuario);
        header('Location: ' . Sesion::inicioDe($usuario['rol']));
        exit;
    }
    $error = 'Correo o contrasena incorrectos';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StockFlow | Gestion de Stock y Ventas</title>
<link rel="stylesheet" href="css/login.css">
</head>
<body>
<main class="login">
  <form class="login-box" method="post">
    <div class="brand"><div class="logo"><img src="img/logo.png" alt="Logo"></div>StockFlow<span></span></div>
    <p class="sub">Sistema de Gestion de Stock y Ventas</p>
    <label>Correo</label>
    <input name="email" type="email" required>
    <label>Contrasena</label>
    <input name="password" type="password" required>
    <button class="btn full">Entrar</button>
    <?php if ($error): ?><div class="msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <p class="foot">HuskyTech Soluciones · CETP-UTU · BT Informatica 2026</p>
  </form>
</main>
</body>
</html>
