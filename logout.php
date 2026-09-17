<?php
require __DIR__ . '/config/config.php';
Sesion::cerrar();
header('Location: index.php');
exit;
