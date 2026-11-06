<?php

abstract class DAO
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    protected function consultar(string $sql, array $params = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    protected function fila(string $sql, array $params = []): ?array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $fila = $st->fetch();
        return $fila === false ? null : $fila;
    }

    protected function ejecutar(string $sql, array $params = []): void
    {
        $this->db->prepare($sql)->execute($params);
    }

    protected function ultimoId(): int
    {
        return (int)$this->db->lastInsertId();
    }
}
