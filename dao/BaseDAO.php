<?php
abstract class BaseDAO
{
    protected static function pdo(): PDO
    {
        return Conexion::obtener();
    }
}
