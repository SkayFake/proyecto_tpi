<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Usuario {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    /**
     * $correo  y $contrasenia deben llegarte YA encriptados
     * (los encriptas en el controlador antes de llamar a este método)
     */
    public function getUsuarioLogin(string $correo, string $contrasenia): ?array {
        try {
            $sql = "SELECT 
                        id_odontologo,      -- cámbialo si tu PK se llama distinto (id, id_usuario, etc.)
                        nombre,
                        correo,
                        telefono,
                        especialidad,
                        es_admin,
                        estado,
                        created_at
                    FROM odontologo
                    WHERE correo = :correo
                      AND contrasenia = :contrasenia
                      AND estado = 1      -- solo permite login a activos (opcional)
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':contrasenia', $contrasenia, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;

        } catch (Throwable $e) {
            error_log("Error getUsuarioLogin: " . $e->getMessage());
            return null;
        }
    }
}
