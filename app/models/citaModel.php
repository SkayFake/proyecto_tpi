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


    public function buscarPacientePorCorreo(string $correo): ?array
    {
        try {
            $sql = "SELECT id_paciente, nombre 
                    FROM paciente 
                    WHERE correo = :correo
                    LIMIT 1";

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
                        p.id_paciente,
                        p.nombre  AS nombre_paciente,
                        p.correo  AS correo_paciente,
                        o.id_odontologo,
                        o.nombre AS nombre_odontologo
                    FROM cita c
                    INNER JOIN paciente   p ON c.id_paciente   = p.id_paciente
                    INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                    ORDER BY c.fecha_cita, c.hora_cita";

            return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

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
                        p.id_paciente,
                        p.nombre AS nombre_paciente,
                        p.correo AS correo_paciente,
                        o.id_odontologo,
                        o.nombre AS nombre_odontologo
                    FROM cita c
                    INNER JOIN paciente   p ON c.id_paciente   = p.id_paciente
                    INNER JOIN odontologo o ON c.id_odontologo = o.id_odontologo
                    WHERE c.id_cita = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id', $id_cita, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Throwable $e) {
            error_log("Error getCitaById: " . $e->getMessage());
            return null;
        }
    }

    public function existeCita(int $id_odontologo, string $fecha_cita, string $hora_cita): bool
    {
        try {
            $sql = "SELECT COUNT(*) 
                    FROM cita
                    WHERE id_odontologo = :id_odontologo
                    AND fecha_cita = :fecha_cita
                    AND hora_cita = :hora_cita";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':fecha_cita', $fecha_cita);
            $stmt->bindValue(':hora_cita', $hora_cita);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;

        } catch (Throwable $e) {
            error_log("Error existeCita: " . $e->getMessage());
            return false;
        }
    }

    public function existeOtraCita(int $id_cita, int $id_odontologo, string $fecha_cita, string $hora_cita): bool
    {
        try {
            $sql = "SELECT COUNT(*) 
                    FROM cita
                    WHERE id_odontologo = :id_odontologo
                    AND fecha_cita = :fecha_cita
                    AND hora_cita = :hora_cita
                    AND id_cita <> :id_cita";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_cita', $id_cita);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':fecha_cita', $fecha_cita);
            $stmt->bindValue(':hora_cita', $hora_cita);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;

        } catch (Throwable $e) {
            error_log("Error existeOtraCita: " . $e->getMessage());
            return false;
        }
    }


    public function hayDisponibilidad(int $id_odontologo, string $fecha_cita, string $hora_cita): bool
    {
        try {
            

            return true;

        } catch (Throwable $e) {
            error_log("Error hayDisponibilidad: " . $e->getMessage());
            return false;
        }
    }

    public function agregar(
        int $id_paciente,
        int $id_odontologo,
        string $fecha_cita,
        string $hora_cita,
        ?string $motivo,
        string $estado
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_cita_insert(
                        :id_paciente,
                        :id_odontologo,
                        :fecha_cita,
                        :hora_cita,
                        :motivo,
                        :estado
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_paciente', $id_paciente);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':fecha_cita', $fecha_cita);
            $stmt->bindValue(':hora_cita', $hora_cita);
            $stmt->bindValue(':motivo', $motivo ?: null);
            $stmt->bindValue(':estado', $estado);

            $stmt->execute();
            $this->conexion->commit();

            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregar cita: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(
        int $id_cita,
        int $id_paciente,
        int $id_odontologo,
        string $fecha_cita,
        string $hora_cita,
        ?string $motivo,
        string $estado
    ): bool {
        try {
            $this->conexion->beginTransaction();

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

            $stmt->bindValue(':id_cita', $id_cita);
            $stmt->bindValue(':id_paciente', $id_paciente);
            $stmt->bindValue(':id_odontologo', $id_odontologo);
            $stmt->bindValue(':fecha_cita', $fecha_cita);
            $stmt->bindValue(':hora_cita', $hora_cita);
            $stmt->bindValue(':motivo', $motivo ?: null);
            $stmt->bindValue(':estado', $estado);

            $stmt->execute();
            $this->conexion->commit();

            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar cita: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id_cita): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_cita_delete(:id_cita)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_cita', $id_cita);
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
