<?php
declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Tratamiento
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    /**
     * Listar TODOS los tratamientos con datos relacionados
     */
    public function getTratamientos(): array
    {
        try {
            $sql = "SELECT 
                        t.id_tratamiento,
                        t.id_paciente,
                        t.id_odontologo,
                        t.id_servicio,
                        t.fecha_fin,
                        t.estado,
                        t.notas,
                        t.created_at,
                        p.nombre     AS nombre_paciente,
                        p.correo     AS correo_paciente,
                        o.nombre     AS nombre_odontologo,
                        s.nombre     AS nombre_servicio
                    FROM tratamiento t
                    LEFT JOIN paciente   p ON t.id_paciente   = p.id_paciente
                    LEFT JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    LEFT JOIN servicio   s ON t.id_servicio   = s.id_servicio
                    ORDER BY t.created_at DESC";

            return $this->conexion
                        ->query($sql)
                        ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getTratamientos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un tratamiento por su ID
     */
    public function getTratamientoById(int $id_tratamiento): ?array
    {
        try {
            $sql = "SELECT 
                        t.id_tratamiento,
                        t.id_paciente,
                        t.id_odontologo,
                        t.id_servicio,
                        t.fecha_fin,
                        t.estado,
                        t.notas,
                        t.created_at,
                        p.nombre     AS nombre_paciente,
                        p.correo     AS correo_paciente,
                        o.nombre     AS nombre_odontologo,
                        s.nombre     AS nombre_servicio
                    FROM tratamiento t
                    INNER JOIN paciente   p ON t.id_paciente   = p.id_paciente
                    INNER JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    INNER JOIN servicio   s ON t.id_servicio   = s.id_servicio
                    WHERE t.id_tratamiento = :id
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id', $id_tratamiento, PDO::PARAM_INT);
            $stmt->execute();

            error_log("DEBUG getTratamientos rows: " . json_encode($stmt));

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error getTratamientoById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar tratamientos por paciente (historial)
     */
    public function getTratamientosPorPaciente(int $id_paciente): array
    {
        try {
            $sql = "SELECT 
                        t.id_tratamiento,
                        t.id_paciente,
                        t.id_odontologo,
                        t.id_servicio,
                        t.fecha_fin,
                        t.estado,
                        t.notas,
                        t.created_at,
                        o.nombre     AS nombre_odontologo,
                        s.nombre_servicio AS nombre_servicio
                    FROM tratamiento t
                    INNER JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    INNER JOIN servicio   s ON t.id_servicio   = s.id_servicio
                    WHERE t.id_paciente = :id_paciente
                    ORDER BY t.created_at DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getTratamientosPorPaciente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Agregar tratamiento
     * $fecha_fin puede venir null (tratamiento en curso)
     */
    public function agregar(
        int $id_paciente,
        int $id_odontologo,
        int $id_servicio,
        ?string $fecha_fin,
        string $estado,
        ?string $notas
    ): bool {
        try {
            $this->conexion->beginTransaction();

            error_log("Insertando tratamiento: id_paciente=$id_paciente, id_odontologo=$id_odontologo, id_servicio=$id_servicio, fecha_fin=$fecha_fin, estado=$estado, notas=$notas");


            $sql = "INSERT INTO tratamiento (
                        id_paciente,
                        id_odontologo,
                        id_servicio,
                        fecha_fin,
                        estado,
                        notas
                    ) VALUES (
                        :id_paciente,
                        :id_odontologo,
                        :id_servicio,
                        :fecha_fin,
                        :estado,
                        :notas
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_paciente',  $id_paciente);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':id_servicio',  $id_servicio);

            if ($fecha_fin && trim($fecha_fin) !== '') {
                $stmt->bindValue(':fecha_fin', $fecha_fin);
            } else {
                $stmt->bindValue(':fecha_fin', null);
            }

            $stmt->bindValue(':estado', $estado);

            if ($notas && trim($notas) !== '') {
                $stmt->bindValue(':notas', $notas);
            } else {
                $stmt->bindValue(':notas', null);
            }

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregar tratamiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar tratamiento
     */
    public function actualizar(
        int $id_tratamiento,
        int $id_paciente,
        int $id_odontologo,
        int $id_servicio,
        ?string $fecha_fin,
        string $estado,
        ?string $notas
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "UPDATE tratamiento
                    SET id_paciente   = :id_paciente,
                        id_odontologo = :id_odontologo,
                        id_servicio   = :id_servicio,
                        fecha_fin     = :fecha_fin,
                        estado        = :estado,
                        notas         = :notas
                    WHERE id_tratamiento = :id_tratamiento";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_tratamiento', $id_tratamiento);
            $stmt->bindValue(':id_paciente',   $id_paciente);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':id_servicio',   $id_servicio);

            if ($fecha_fin && trim($fecha_fin) !== '') {
                $stmt->bindValue(':fecha_fin', $fecha_fin);
            } else {
                $stmt->bindValue(':fecha_fin', null);
            }

            $stmt->bindValue(':estado', $estado);

            if ($notas && trim($notas) !== '') {
                $stmt->bindValue(':notas', $notas);
            } else {
                $stmt->bindValue(':notas', null);
            }

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar tratamiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar tratamiento
     */
    public function eliminar(int $id_tratamiento): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "DELETE FROM tratamiento
                    WHERE id_tratamiento = :id_tratamiento";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar tratamiento: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPacientePorCorreo(string $correo): ?array
    {
        try {
            $sql = "SELECT id_paciente, nombre 
                    FROM paciente 
                    WHERE correo = :correo
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":correo", $correo);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error buscarPacientePorCorreo: " . $e->getMessage());
            return null;
        }
    }

    public function getTratamientosSelect(): array
{
    try {
        $sql = "SELECT id_servicio,nombre  AS nombre_tratamiento
                FROM servicio
                ORDER BY nombre ASC";
        
        return $this->conexion
                    ->query($sql)
                    ->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log("Error getTratamientos: " . $e->getMessage());
        return [];
    }
}
}
