<?php
declare(strict_types=1);

final class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name('stockflow_sid');
        session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function entrar(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => (int)$usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
        ];
    }

    public static function salir(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function requiere(array $roles = []): array
    {
        $usuario = self::usuario();
        if (!$usuario) {
            throw new ApiException('no_autenticado', 401);
        }
        if ($roles && !in_array($usuario['rol'], $roles, true)) {
            throw new ApiException('sin_permiso', 403);
        }
        return $usuario;
    }
}
