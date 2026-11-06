<?php

final class Validador
{
    public static function validar(array $entrada, array $reglas): array
    {
        $salida = [];
        foreach ($reglas as $campo => $regla) {
            $valor = $entrada[$campo] ?? null;
            $vacio = $valor === null || (is_string($valor) && trim($valor) === '');
            if ($vacio) {
                if (!empty($regla['requerido'])) {
                    throw new ApiException('datos', 400, $campo);
                }
                $salida[$campo] = array_key_exists('defecto', $regla) ? $regla['defecto'] : null;
                continue;
            }
            $salida[$campo] = self::convertir($campo, $valor, $regla);
        }
        return $salida;
    }

    public static function lista($valor, string $campo, int $maximo, array $reglas): array
    {
        if (!is_array($valor) || !$valor || count($valor) > $maximo) {
            throw new ApiException('datos', 400, $campo);
        }
        $filas = [];
        foreach (array_values($valor) as $fila) {
            if (!is_array($fila)) {
                throw new ApiException('datos', 400, $campo);
            }
            $filas[] = self::validar($fila, $reglas);
        }
        return $filas;
    }

    private static function convertir(string $campo, $valor, array $regla)
    {
        switch ($regla['tipo']) {
            case 'texto':
                return self::texto($campo, $valor, $regla);
            case 'email':
                return self::email($campo, $valor, $regla);
            case 'entero':
                return self::entero($campo, $valor, $regla);
            case 'decimal':
                return self::decimal($campo, $valor, $regla);
            case 'fecha':
                return self::fecha($campo, $valor);
            case 'enum':
                return self::enumerado($campo, $valor, $regla);
            case 'booleano':
                return self::booleano($campo, $valor);
        }
        throw new ApiException('datos', 400, $campo);
    }

    private static function texto(string $campo, $valor, array $regla): string
    {
        if (!is_string($valor) && !is_int($valor) && !is_float($valor)) {
            throw new ApiException('datos', 400, $campo);
        }
        $texto = (string)$valor;
        if (($regla['recortar'] ?? true) === true) {
            $texto = trim($texto);
        }
        $largo = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
        if ($largo < ($regla['min'] ?? 1) || $largo > ($regla['max'] ?? 255)) {
            throw new ApiException('datos', 400, $campo);
        }
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $texto)) {
            throw new ApiException('datos', 400, $campo);
        }
        return $texto;
    }

    private static function email(string $campo, $valor, array $regla): string
    {
        $texto = is_string($valor) ? trim($valor) : '';
        if ($texto === '' || strlen($texto) > ($regla['max'] ?? 150) || !filter_var($texto, FILTER_VALIDATE_EMAIL)) {
            throw new ApiException('datos', 400, $campo);
        }
        return $texto;
    }

    private static function entero(string $campo, $valor, array $regla): int
    {
        if (is_string($valor) && preg_match('/^-?\d{1,10}$/', trim($valor))) {
            $valor = (int)trim($valor);
        }
        if (!is_int($valor)) {
            throw new ApiException('datos', 400, $campo);
        }
        if ($valor < ($regla['min'] ?? PHP_INT_MIN) || $valor > ($regla['max'] ?? PHP_INT_MAX)) {
            throw new ApiException('datos', 400, $campo);
        }
        return $valor;
    }

    private static function decimal(string $campo, $valor, array $regla): float
    {
        if (is_string($valor) && preg_match('/^-?\d{1,10}(\.\d{1,4})?$/', trim($valor))) {
            $valor = (float)trim($valor);
        }
        if (is_int($valor)) {
            $valor = (float)$valor;
        }
        if (!is_float($valor) || is_nan($valor) || is_infinite($valor)) {
            throw new ApiException('datos', 400, $campo);
        }
        if ($valor < ($regla['min'] ?? -1.0E+12) || $valor > ($regla['max'] ?? 1.0E+12)) {
            throw new ApiException('datos', 400, $campo);
        }
        return round($valor, 2);
    }

    private static function fecha(string $campo, $valor): string
    {
        $texto = is_string($valor) ? trim($valor) : '';
        $fecha = DateTime::createFromFormat('!Y-m-d', $texto);
        if (!$fecha || $fecha->format('Y-m-d') !== $texto) {
            throw new ApiException('datos', 400, $campo);
        }
        return $texto;
    }

    private static function enumerado(string $campo, $valor, array $regla): string
    {
        if (!is_string($valor) || !in_array($valor, $regla['valores'], true)) {
            throw new ApiException('datos', 400, $campo);
        }
        return $valor;
    }

    private static function booleano(string $campo, $valor): int
    {
        if ($valor === true || $valor === 1 || $valor === '1') {
            return 1;
        }
        if ($valor === false || $valor === 0 || $valor === '0') {
            return 0;
        }
        throw new ApiException('datos', 400, $campo);
    }
}
