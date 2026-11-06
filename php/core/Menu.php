<?php

final class Menu
{
    private const ITEMS = [
        ['id' => 'dashboard', 'clave' => 'dashboard', 'roles' => ['admin', 'repositor']],
        ['id' => 'inventario', 'clave' => 'inventory', 'roles' => ['admin', 'repositor', 'vendedor']],
        ['id' => 'ventas', 'clave' => 'sales', 'roles' => ['admin', 'vendedor']],
        ['id' => 'gastos', 'clave' => 'expenses', 'roles' => ['admin']],
        ['id' => 'reportes', 'clave' => 'reports', 'roles' => ['admin']],
        ['id' => 'usuarios', 'clave' => 'users', 'roles' => ['admin']],
        ['id' => 'portal', 'clave' => 'portal', 'roles' => ['cliente', 'admin']],
    ];

    public static function paraRol(string $rol): array
    {
        return array_values(array_filter(self::ITEMS, fn(array $i) => in_array($rol, $i['roles'], true)));
    }

    public static function inicio(string $rol): string
    {
        switch ($rol) {
            case 'admin':
                return 'dashboard';
            case 'vendedor':
                return 'ventas';
            case 'repositor':
                return 'inventario';
            default:
                return 'portal';
        }
    }
}
