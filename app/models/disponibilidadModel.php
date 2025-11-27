<?php
declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Disponibilidad
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }


    public function listar(): array
    {
        try {
            $sql = "SELECT d.*, o.nombre AS nombre_odontologo
                    FROM disponibilidad d
                    INNER JOIN odontologo o ON o.id_odontologo = d.id_odontologo
                    ORDER BY d.id_disponibilidad DESC";

            return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        } catch (Throwable $e) {
            error_log("Error listar disponibilidades: " . $e->getMessage());
            return [];
        }
    }

    public function obtener(int $id): ?array
    {
        try {
            $sql = "SELECT * 
                    FROM disponibilidad 
                    WHERE id_disponibilidad = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Throwable $e) {
            error_log("Error obtener disponibilidad: " . $e->getMessage());
            return null;
        }
    }

    public function existeTraslape(int $id_odontologo, string $fi, string $ff, int $ignore_id = 0): bool
    {
        try {
            $sql = "SELECT COUNT(*) 
                    FROM disponibilidad
                    WHERE id_odontologo = :id_odontologo
                      AND id_disponibilidad <> :ignore
                      AND (
                            fecha_inicio <= :fin 
                        AND fecha_fin    >= :inicio
                      )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_odontologo", $id_odontologo);
            $stmt->bindValue(":inicio", $fi);
            $stmt->bindValue(":fin", $ff);
            $stmt->bindValue(":ignore", $ignore_id);

            $stmt->execute();

            return $stmt->fetchColumn() > 0;

        } catch (Throwable $e) {
            error_log("Error traslape disponibilidad: " . $e->getMessage());
            return true;
        }
    }


    public function existeTraslapeCompleto(
        int $id_odontologo,
        string $fi, string $ff,
        string $hi, string $hf,
        int $ignore_id = 0
    ): bool {

        try {

            $sql = "SELECT COUNT(*) 
                    FROM disponibilidad
                    WHERE id_odontologo = :id_odontologo
                      AND id_disponibilidad <> :ignore
                      AND (
                            fecha_inicio <= :finFecha
                        AND fecha_fin    >= :iniFecha
                      )
                      AND (
                            hora_inicio < :finHora
                        AND hora_fin    > :iniHora
                      )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_odontologo", $id_odontologo);
            $stmt->bindValue(":iniFecha", $fi);
            $stmt->bindValue(":finFecha", $ff);
            $stmt->bindValue(":iniHora", $hi);
            $stmt->bindValue(":finHora", $hf);
            $stmt->bindValue(":ignore", $ignore_id);

            $stmt->execute();

            return $stmt->fetchColumn() > 0;

        } catch (Throwable $e) {
            error_log("Error traslape completo disponibilidad: " . $e->getMessage());
            return true;
        }
    }

    public function citasFueraDeHorario(
        int $id_odontologo,
        string $fi, string $ff,
        string $hi, string $hf
    ): bool {

        try {
            $sql = "SELECT COUNT(*)
                    FROM cita
                    WHERE id_odontologo = :id_odontologo
                      AND fecha_cita BETWEEN :fi AND :ff
                      AND (
                            hora_cita < :hi
                         OR hora_cita > :hf
                      )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_odontologo", $id_odontologo);
            $stmt->bindValue(":fi", $fi);
            $stmt->bindValue(":ff", $ff);
            $stmt->bindValue(":hi", $hi);
            $stmt->bindValue(":hf", $hf);

            $stmt->execute();

            return $stmt->fetchColumn() > 0; 

        } catch (Throwable $e) {
            error_log("Error validar citas fuera de horario: " . $e->getMessage());
            return true;
        }
    }


  public function agregar(int $id_odontologo, string $fecha_inicio, string $fecha_fin,
                        string $hora_inicio, string $hora_fin, ?string $notas, int $cupo): bool
{
    try {
        $sql = "INSERT INTO disponibilidad
                (id_odontologo, fecha_inicio, fecha_fin, hora_inicio, hora_fin, notas, cupo)
                VALUES
                (:id_odontologo, :fecha_inicio, :fecha_fin, :hora_inicio, :hora_fin, :notas, :cupo)";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindValue(":id_odontologo", $id_odontologo, PDO::PARAM_INT);
        $stmt->bindValue(":fecha_inicio", $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_fin", $fecha_fin, PDO::PARAM_STR);
        $stmt->bindValue(":hora_inicio", $hora_inicio, PDO::PARAM_STR);
        $stmt->bindValue(":hora_fin", $hora_fin, PDO::PARAM_STR);
        $stmt->bindValue(":notas", $notas, PDO::PARAM_STR);
        $stmt->bindValue(":cupo", $cupo, PDO::PARAM_INT); 

        return $stmt->execute();

    } catch (Throwable $e) {
        error_log("Error agregar disponibilidad: " . $e->getMessage());
        return false;
    }
}



public function actualizar(int $id, int $id_odontologo, string $fecha_inicio, string $fecha_fin,
                           string $hora_inicio, string $hora_fin, ?string $notas, int $cupo): bool
{
    try {
        $sql = "UPDATE disponibilidad
                SET id_odontologo = :id_odontologo,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    notas = :notas,
                    cupo = :cupo
                WHERE id_disponibilidad = :id_disponibilidad";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindValue(":id_disponibilidad", $id, PDO::PARAM_INT);
        $stmt->bindValue(":id_odontologo", $id_odontologo, PDO::PARAM_INT);
        $stmt->bindValue(":fecha_inicio", $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_fin", $fecha_fin, PDO::PARAM_STR);
        $stmt->bindValue(":hora_inicio", $hora_inicio, PDO::PARAM_STR);
        $stmt->bindValue(":hora_fin", $hora_fin, PDO::PARAM_STR);
        $stmt->bindValue(":notas", $notas, PDO::PARAM_STR);
        $stmt->bindValue(":cupo", $cupo, PDO::PARAM_INT); // Agregar cupo

        return $stmt->execute();

    } catch (Throwable $e) {
        error_log("Error actualizar disponibilidad: " . $e->getMessage());
        return false;
    }
}





    public function eliminar(int $id): bool
    {
        try {
            $sql = "DELETE FROM disponibilidad 
                    WHERE id_disponibilidad = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id", $id);
            return $stmt->execute();

        } catch (Throwable $e) {
            error_log("Error eliminar disponibilidad: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorFecha(int $id_odontologo, string $fecha): ?array
{
    try {
        $sql = "SELECT *
                FROM disponibilidad
                WHERE id_odontologo = :id
                AND :fecha BETWEEN fecha_inicio AND fecha_fin
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id", $id_odontologo, PDO::PARAM_INT);
        $stmt->bindValue(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    } catch (Throwable $e) {
        error_log("Error obtenerPorFecha: " . $e->getMessage());
        return null;
    }
}

public function hayCupoDisponible(int $id_odontologo, string $fecha_inicio, string $fecha_fin): bool
{
    try {
        // Consultamos la disponibilidad existente para el odontólogo y las fechas especificadas
        $sql = "SELECT cupo 
                FROM disponibilidad 
                WHERE id_odontologo = :id_odontologo
                  AND fecha_inicio <= :fecha_fin
                  AND fecha_fin >= :fecha_inicio
                  AND cupo > 0";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_odontologo", $id_odontologo, PDO::PARAM_INT);
        $stmt->bindValue(":fecha_inicio", $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_fin", $fecha_fin, PDO::PARAM_STR);
        $stmt->execute();

        $cupo = $stmt->fetchColumn();

        // Si el resultado es mayor a 0, significa que hay cupos disponibles
        return $cupo > 0;

    } catch (Throwable $e) {
        error_log("Error verificar disponibilidad de cupo: " . $e->getMessage());
        return false;
    }
}

public function reservarCupo(int $id_disponibilidad): bool
{
    try {
        // Decrementar el cupo disponible en la tabla de disponibilidad
        $sql = "UPDATE disponibilidad
                SET cupo = cupo - 1
                WHERE id_disponibilidad = :id_disponibilidad
                  AND cupo > 0"; // Solo decrementa si hay cupos disponibles

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_disponibilidad", $id_disponibilidad, PDO::PARAM_INT);

        return $stmt->execute();

    } catch (Throwable $e) {
        error_log("Error reservar cupo: " . $e->getMessage());
        return false;
    }
}


public function liberarCupo(int $id_disponibilidad): bool
{
    try {
        // Incrementar el cupo disponible en la tabla de disponibilidad
        $sql = "UPDATE disponibilidad
                SET cupo = cupo + 1
                WHERE id_disponibilidad = :id_disponibilidad";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(":id_disponibilidad", $id_disponibilidad, PDO::PARAM_INT);

        return $stmt->execute();

    } catch (Throwable $e) {
        error_log("Error liberar cupo: " . $e->getMessage());
        return false;
    }
}



}
