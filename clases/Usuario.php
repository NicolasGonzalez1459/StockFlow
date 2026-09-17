<?php
class Usuario
{
    public ?int $id = null;
    public string $nombre = '';
    public string $email = '';
    public string $password = '';
    public string $rol = 'vendedor';
    public int $activo = 1;

    public static function autenticar(string $email, string $password): ?array
    {
        $fila = UsuarioDAO::autenticar($email);
        if (!$fila || !password_verify($password, $fila['password'])) return null;
        return [
            'id' => $fila['id'],
            'nombre' => $fila['nombre'],
            'email' => $fila['email'],
            'rol' => $fila['rol']
        ];
    }

    public static function listarTodos(): array
    {
        return UsuarioDAO::listarTodos();
    }

    public static function buscarPorId(int $id): ?array
    {
        return UsuarioDAO::buscarPorId($id);
    }

    public function crear(): int
    {
        $pdo = Conexion::obtener();
        $st = $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)');
        $st->execute([$this->nombre, $this->email, password_hash($this->password, PASSWORD_DEFAULT), $this->rol]);
        return (int) $pdo->lastInsertId();
    }

    public function actualizar(): void
    {
        $pdo = Conexion::obtener();
        $st = $pdo->prepare('UPDATE usuarios SET nombre=?,email=?,rol=?,activo=? WHERE id=?');
        $st->execute([$this->nombre, $this->email, $this->rol, $this->activo, $this->id]);
        if (!empty($this->password)) {
            $p = $pdo->prepare('UPDATE usuarios SET password=? WHERE id=?');
            $p->execute([password_hash($this->password, PASSWORD_DEFAULT), $this->id]);
        }
    }

    public static function eliminar(int $id): void
    {
        $st = Conexion::obtener()->prepare('UPDATE usuarios SET activo=0 WHERE id=?');
        $st->execute([$id]);
    }
}
