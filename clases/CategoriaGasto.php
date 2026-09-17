<?php
class CategoriaGasto
{
    public static function listarTodas(): array
    {
        return Conexion::obtener()->query('SELECT * FROM categorias_gasto ORDER BY nombre')->fetchAll();
    }
}
