<?php
class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function usuarioActual(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function iniciarSesionUsuario(array $usuario): void
    {
        $_SESSION['usuario'] = $usuario;
    }

    public static function cerrar(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function requiere(array $roles = []): array
    {
        $u = self::usuarioActual();
        if (!$u) {
            header('Location: index.php');
            exit;
        }
        if ($roles && !in_array($u['rol'], $roles, true)) {
            header('Location: ' . self::inicioDe($u['rol']));
            exit;
        }
        return $u;
    }

    public static function inicioDe(string $rol): string
    {
        if ($rol === 'admin') return 'dashboard.php';
        if ($rol === 'vendedor') return 'ventas.php';
        if ($rol === 'repositor') return 'inventario.php';
        return 'portal.php';
    }
}
