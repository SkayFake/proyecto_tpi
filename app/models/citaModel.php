<?php
declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Cita
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }
    
    private function obtenerIdPacientePorDui(string $dui): ?int
    {
        try {
            $sql = "SELECT id_paciente 
                    FROM paciente 
                    WHERE dui = :dui
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dui', $dui, PDO::PARAM_STR);
            $stmt->execute();

            $id = $stmt->fetchColumn();
            return $id !== false ? (int)$id : null;
        } catch (Throwable $e) {
            error_log("Error obtenerIdPacientePorDui: " . $e->getMessage());
            return null;
        }
    }

    public function buscarPacientePorCorreo($correo)
{
    try {
        $sql = "SELECT id_paciente, nombre 
                FROM paciente 
                WHERE correo = :correo";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    } catch (Throwable $e) {
        error_log("Error buscarPacientePorCorreo: " . $e->getMessage());
        return null;
    }
}


    public function getCitas(): array
    {
        try {
            $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        c.hora_cita,
                        c.motivo,
                        c.estado,
                        c.id_paciente,
                        p.nombre  AS nombre_paciente,
                        p.dui     AS dui_paciente,
                        c.id_odontologo,
                        o.nombre  AS nombre_odontologo
                    FROM cita c
                    INNER JOIN paciente   p ON c.id_paciente   = p.id_paciente
                    INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                    ORDER BY c.fecha_cita, c.hora_cita";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getCitas: " . $e->getMessage());
            return [];
        }
    }

    public function getCitaById(int $id_cita): ?array
    {
        try {
            $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        c.hora_cita,
                        c.motivo,
                        c.estado,
                        c.id_paciente,
                        p.nombre  AS nombre_paciente,
                        p.dui     AS dui_paciente,
                        c.id_odontologo,
                        o.nombre  AS nombre_odontologo
                    FROM cita c
                    INNER JOIN paciente   p ON c.id_paciente   = p.id_paciente
                    INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                    WHERE c.id_cita = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id', $id_cita, PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ?: null;
        } catch (Throwable $e) {
            error_log("Error getCitaById: " . $e->getMessage());
            return null;
        }
    }

    public function agregarPorDui(
        string $dui_paciente,
        int $id_odontologo,
        string $fecha_cita,  
        string $hora_cita,    
        ?string $motivo,
        string $estado = 'programada'
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $id_paciente = $this->obtenerIdPacientePorDui($dui_paciente);
            if ($id_paciente === null) {
                $this->conexion->rollBack();
                return false;
            }

            $sql = "CALL sp_cita_insert(
                        :id_paciente,
                        :id_odontologo,
                        :fecha_cita,
                        :hora_cita,
                        :motivo,
                        :estado
                    )";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_paciente',   $id_paciente,   PDO::PARAM_INT);
            $stmt->bindValue(':id_odontologo', $id_odontologo, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_cita',    $fecha_cita,    PDO::PARAM_STR);
            $stmt->bindValue(':hora_cita',     $hora_cita,     PDO::PARAM_STR);
            $stmt->bindValue(':motivo',        $motivo,        $motivo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':estado',        $estado,        PDO::PARAM_STR);

            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregarPorDui (cita): " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPorDui(
        int $id_cita,
        string $dui_paciente,
        int $id_odontologo,
        string $fecha_cita,
        string $hora_cita,
        ?string $motivo,
        string $estado
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $id_paciente = $this->obtenerIdPacientePorDui($dui_paciente);
            if ($id_paciente === null) {
                $this->conexion->rollBack();
                return false;
            }

            $sql = "CALL sp_cita_update(
                        :id_cita,
                        :id_paciente,
                        :id_odontologo,
                        :fecha_cita,
                        :hora_cita,
                        :motivo,
                        :estado
                    )";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_cita',       $id_cita,       PDO::PARAM_INT);
            $stmt->bindValue(':id_paciente',   $id_paciente,   PDO::PARAM_INT);
            $stmt->bindValue(':id_odontologo', $id_odontologo, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_cita',    $fecha_cita,    PDO::PARAM_STR);
            $stmt->bindValue(':hora_cita',     $hora_cita,     PDO::PARAM_STR);
            $stmt->bindValue(':motivo',        $motivo,        $motivo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':estado',        $estado,        PDO::PARAM_STR);

            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizarPorDui (cita): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id_cita): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_cita_delete(:id_cita)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_cita', $id_cita, PDO::PARAM_INT);

            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar cita: " . $e->getMessage());
            return false;
        }
    }
}
