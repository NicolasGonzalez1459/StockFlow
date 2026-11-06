<?php

final class CategoriaGastoDAO extends DAO
{
    public function listar(): array
    {
        return $this->consultar('SELECT id,nombre FROM categorias_gasto ORDER BY nombre');
    }
}
