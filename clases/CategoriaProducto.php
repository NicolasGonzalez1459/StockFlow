<?php
class CategoriaProducto
{
    public static function listarTodas(): array
    {
        return Conexion::obtener()->query('SELECT * FROM categorias_producto ORDER BY nombre')->fetchAll();
    }
}
