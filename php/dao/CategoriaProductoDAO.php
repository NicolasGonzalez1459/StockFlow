<?php

final class CategoriaProductoDAO extends DAO
{
    public function listar(): array
    {
        return $this->consultar('SELECT id,nombre FROM categorias_producto ORDER BY nombre');
    }
}
