<?php

final class Api
{
    public static function ejecutar(array $manejadores): void
    {
        ini_set('display_errors', '0');
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        try {
            Config::cargar(dirname(__DIR__, 2));
            date_default_timezone_set((string)Config::get('zona_horaria', 'UTC'));
            Sesion::iniciar();
            $peticion = new Peticion();
            $metodo = $peticion->metodo();
            if (!isset($manejadores[$metodo])) {
                header('Allow: ' . implode(', ', array_keys($manejadores)));
                throw new ApiException('metodo', 405);
            }
            $manejadores[$metodo]($peticion);
            throw new ApiException('error', 500);
        } catch (ApiException $e) {
            Respuesta::error($e->getMessage(), $e->estado(), $e->detalle());
        } catch (Throwable $e) {
            error_log('[StockFlow API] ' . get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            Respuesta::error('error', 500);
        }
    }
}
