<?php

final class Peticion
{
    private ?array $cuerpo = null;

    public function metodo(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function id(): ?int
    {
        if (!isset($_GET['id'])) {
            return null;
        }
        $valor = $_GET['id'];
        if (!is_string($valor) || !ctype_digit($valor) || (int)$valor < 1) {
            throw new ApiException('datos', 400, 'id');
        }
        return (int)$valor;
    }

    public function idObligatorio(): int
    {
        $id = $this->id();
        if ($id === null) {
            throw new ApiException('datos', 400, 'id');
        }
        return $id;
    }

    public function cuerpo(): array
    {
        if ($this->cuerpo !== null) {
            return $this->cuerpo;
        }
        $tipo = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
        if (strpos($tipo, 'application/json') === false) {
            throw new ApiException('formato_json', 415);
        }
        $datos = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            throw new ApiException('datos', 400);
        }
        $this->cuerpo = $datos;
        return $this->cuerpo;
    }

    public function consulta(string $clave, $defecto = null)
    {
        $valor = $_GET[$clave] ?? $defecto;
        return is_string($valor) ? $valor : $defecto;
    }

    public function formulario(): array
    {
        return $_POST;
    }

    public function archivo(string $clave): ?array
    {
        return $_FILES[$clave] ?? null;
    }
}
