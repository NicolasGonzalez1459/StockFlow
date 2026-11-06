<?php

final class ApiException extends Exception
{
    private int $estado;
    private string $detalle;

    public function __construct(string $codigo, int $estado = 400, string $detalle = '')
    {
        parent::__construct($codigo);
        $this->estado = $estado;
        $this->detalle = $detalle;
    }

    public function estado(): int
    {
        return $this->estado;
    }

    public function detalle(): string
    {
        return $this->detalle;
    }
}
