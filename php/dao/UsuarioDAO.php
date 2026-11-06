<?php

final class UsuarioDAO extends DAO
{
    public const ROLES = ['admin', 'vendedor', 'repositor', 'cliente'];

    public function autenticar(string $email, string $clave): ?array
    {
        $u = $this->fila('SELECT id,nombre,email,password,rol FROM usuarios WHERE email=? AND activo=1', [$email]);
        if (!$u || !password_verify($clave, $u['password'])) {
            return null;
        }
        return $u;
    }

    public function listar(): array
    {
        return $this->consultar('SELECT id,nombre,email,rol,activo,creado FROM usuarios ORDER BY id');
    }

    public function obtener(int $id): ?array
    {
        return $this->fila('SELECT id,nombre,email,rol,activo,creado FROM usuarios WHERE id=?', [$id]);
    }

    public function crear(array $d): int
    {
        try {
            $this->ejecutar('INSERT INTO usuarios (nombre,email,password,rol,activo) VALUES (?,?,?,?,?)', [
                $d['nombre'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT), $d['rol'], $d['activo'],
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new ApiException('email_duplicado', 400);
            }
            throw $e;
        }
        return $this->ultimoId();
    }

    public function editar(int $id, array $d): void
    {
        try {
            $this->ejecutar('UPDATE usuarios SET nombre=?,email=?,rol=?,activo=? WHERE id=?', [
                $d['nombre'], $d['email'], $d['rol'], $d['activo'], $id,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new ApiException('email_duplicado', 400);
            }
            throw $e;
        }
        if ($d['password'] !== null) {
            $this->ejecutar('UPDATE usuarios SET password=? WHERE id=?', [password_hash($d['password'], PASSWORD_DEFAULT), $id]);
        }
    }

    public function desactivar(int $id): void
    {
        $this->ejecutar('UPDATE usuarios SET activo=0 WHERE id=?', [$id]);
    }
}
