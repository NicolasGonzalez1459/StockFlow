<?php

final class Config
{
    private static ?array $datos = null;

    public static function cargar(string $raiz): void
    {
        $base = require $raiz . '/php/config.php';
        $local = $raiz . '/php/config.local.php';
        if (is_file($local)) {
            $base = array_merge($base, require $local);
        }
        foreach (['db_host' => 'DB_HOST', 'db_nombre' => 'DB_NAME', 'db_usuario' => 'DB_USER', 'db_clave' => 'DB_PASS'] as $clave => $env) {
            $valor = getenv($env);
            if ($valor !== false && $valor !== '') {
                $base[$clave] = $valor;
            }
        }
        $base['raiz'] = $raiz;
        self::$datos = $base;
    }

    public static function get(string $clave, $defecto = null)
    {
        return self::$datos[$clave] ?? $defecto;
    }
}
