<?php

define('DB_HOST', 'host');
define('DB_NAME', 'DB_name');
define('DB_USER', 'user_name');
define('DB_PASS', 'password');

spl_autoload_register(function ($clase) {
    $rutas = [
        __DIR__ . '/../clases/' . $clase . '.php',
        __DIR__ . '/../dao/' . $clase . '.php'
    ];

    foreach ($rutas as $ruta) {
        if (file_exists($ruta)) {
            require $ruta;
            return;
        }
    }
});

Sesion::iniciar();
