<?php

spl_autoload_register(function (string $clase): void {
    $base = __DIR__;
    foreach (['core', 'dao', 'paginas'] as $carpeta) {
        $archivo = $base . '/' . $carpeta . '/' . $clase . '.php';
        if (is_file($archivo)) {
            require $archivo;
            return;
        }
    }
});
