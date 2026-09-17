<?php
class UsuarioDAO extends BaseDAO
{
    public static function autenticar(string $email): ?array
    {
        $st = self::pdo()->prepare('SELECT * FROM usuarios WHERE email=? AND activo=1');
        $st->execute([trim($email)]);
        return $st->fetch() ?: null;
    }

    public static function listarTodos(): array
    {
        return self::pdo()->query('SELECT id,nombre,email,rol,activo,creado FROM usuarios ORDER BY id')->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $st = self::pdo()->prepare('SELECT * FROM usuarios WHERE id=?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
}
