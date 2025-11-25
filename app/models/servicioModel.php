<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Servicio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function agregar(
        string $codigo,
        string $nombre,
        float $precio_base,
        ?string $descripcion,
        int $activo,
        ?string $fecha_vencimiento
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO servicio 
                        (codigo, nombre, descripcion, precio_base, activo, created_at, fecha_vencimiento, notificado_vencimiento)
                    VALUES 
                        (:codigo, :nombre, :descripcion, :precio_base, :activo, NOW(), :fecha_vencimiento, 0)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':precio_base', $precio_base);

            if ($descripcion !== null && $descripcion !== '') {
                $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':descripcion', null, PDO::PARAM_NULL);
            }

            if ($fecha_vencimiento !== null && $fecha_vencimiento !== '') {
                $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':fecha_vencimiento', null, PDO::PARAM_NULL);
            }

            $stmt->bindValue(':activo', $activo, PDO::PARAM_INT);

            $stmt->execute();
            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregar servicio: " . $e->getMessage());
            throw $e;
        }
    }

    public function getServicios(): array
    {
        try {
            $sql = "SELECT 
                        id_servicio,
                        codigo,
                        nombre        AS nombre_servicio,
                        descripcion,
                        precio_base,
                        activo,
                        created_at    AS created_at,
                        fecha_vencimiento
                    FROM servicio
                    ORDER BY id_servicio DESC";

            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Throwable $e) {
            error_log("Error getServicios: " . $e->getMessage());
            return [];
        }
    }
    public function getServicioById(int $id_servicio): ?array
    {
        try {
            $sql = "SELECT 
                        id_servicio,
                        codigo,
                        nombre        AS nombre_servicio,
                        descripcion,
                        precio_base,
                        activo,
                        created_at    AS created_at,
                        fecha_vencimiento
                    FROM servicio
                    WHERE id_servicio = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;

        } catch (Throwable $e) {
            error_log("Error getServicioById: " . $e->getMessage());
            return null;
        }
    }
    public function actualizar(
        int $id_servicio,
        string $codigo,
        string $nombre,
        float $precio_base,
        ?string $descripcion,
        int $activo,
        ?string $fecha_vencimiento,
        ?string $modificado_por = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "UPDATE servicio
                    SET 
                        codigo                = :codigo,
                        nombre                = :nombre,
                        descripcion           = :descripcion,
                        precio_base           = :precio_base,
                        activo                = :activo,
                        fecha_vencimiento     = :fecha_vencimiento,
                        fecha_ult_modificacion = NOW(),
                        modificado_por        = :modificado_por
                    WHERE id_servicio = :id_servicio";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':precio_base', $precio_base);

            if ($descripcion !== null && $descripcion !== '') {
                $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':descripcion', null, PDO::PARAM_NULL);
            }

            if ($fecha_vencimiento !== null && $fecha_vencimiento !== '') {
                $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':fecha_vencimiento', null, PDO::PARAM_NULL);
            }

            if ($modificado_por !== null && $modificado_por !== '') {
                $stmt->bindValue(':modificado_por', $modificado_por, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':modificado_por', null, PDO::PARAM_NULL);
            }

            $stmt->bindValue(':activo', $activo, PDO::PARAM_INT);

            $stmt->execute();
            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar servicio: " . $e->getMessage());
            throw $e;
        }
    }

    public function eliminar(int $id_servicio): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "DELETE FROM servicio WHERE id_servicio = :id_servicio";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar servicio: " . $e->getMessage());
            throw $e;
        }
    }

   //para no repetir nombre en servicio activo
    public function existeNombreActivo(string $nombre, ?int $excepto_id = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM servicio
                    WHERE nombre = :nombre
                      AND activo = 1";

            if ($excepto_id !== null) {
                $sql .= " AND id_servicio <> :id_excluir";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);

            if ($excepto_id !== null) {
                $stmt->bindValue(':id_excluir', $excepto_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$r['total'] > 0;

        } catch (Throwable $e) {
            error_log("Error existeNombreActivo: " . $e->getMessage());
            return false;
        }
    }
}
