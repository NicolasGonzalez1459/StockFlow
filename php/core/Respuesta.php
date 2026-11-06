<?php

final class Respuesta
{
    public static function exito($data = null, string $clave = 'ok', int $estado = 200): void
    {
        self::emitir('success', Idioma::t($clave), $data, $estado);
    }

    public static function error(string $clave, int $estado, string $detalle = ''): void
    {
        $mensaje = Idioma::t($clave);
        if ($detalle !== '') {
            $mensaje .= ' (' . $detalle . ')';
        }
        self::emitir('error', $mensaje, null, $estado);
    }

    public static function redirigir(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    private static function emitir(string $status, string $mensaje, $data, int $estado): void
    {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(
            ['status' => $status, 'message' => $mensaje, 'data' => $data],
            JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
        exit;
    }
}
