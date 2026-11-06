<?php

final class Imagen
{
    private const TIPOS = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    private const MAX_BYTES = 3145728;

    public static function guardar(?array $archivo, int $productoId): string
    {
        if (!$archivo) {
            throw new ApiException('archivo', 400);
        }
        $error = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new ApiException('tamano', 400);
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new ApiException('archivo', 400);
        }
        if ((int)$archivo['size'] > self::MAX_BYTES) {
            throw new ApiException('tamano', 400);
        }
        $info = @getimagesize($archivo['tmp_name']);
        if ($info === false || !isset(self::TIPOS[$info[2]])) {
            throw new ApiException('formato', 400);
        }
        $carpeta = Config::get('raiz') . '/img/productos';
        if (!is_dir($carpeta) && !@mkdir($carpeta, 0755, true)) {
            throw new ApiException('subida', 500);
        }
        $nombre = 'p' . $productoId . '_' . time() . '.' . self::TIPOS[$info[2]];
        if (!@move_uploaded_file($archivo['tmp_name'], $carpeta . '/' . $nombre)) {
            throw new ApiException('subida', 500);
        }
        return 'img/productos/' . $nombre;
    }
}
