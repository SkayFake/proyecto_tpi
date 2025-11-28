<?php
declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Pago
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    
    public function getPagos(): array
    {
        try {
            $sql = "SELECT 
                        p.id_pago,
                        p.id_paciente,
                        p.id_tratamiento,
                        p.metodo_pago,
                        p.monto,
                        p.estado_pago,
                        p.referencia,
                        p.created_at,
                        pac.nombre AS nombre_paciente,
                        tra.nombre AS nombre_tratamiento
                    FROM pago p
                    LEFT JOIN paciente pac ON p.id_paciente = pac.id_paciente
                    LEFT JOIN tratamiento tra ON p.id_tratamiento = tra.id_tratamiento
                    ORDER BY p.created_at DESC";

            return $this->conexion
                        ->query($sql)
                        ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPagos: " . $e->getMessage());
            return [];
        }
    }

    
    public function getPagoById(int $id_pago): ?array
    {
        try {
            $sql = "SELECT 
                        p.id_pago,
                        p.id_paciente,
                        p.id_tratamiento,
                        p.metodo_pago,
                        p.monto,
                        p.estado_pago,
                        p.referencia,
                        p.created_at,
                        pac.nombre AS nombre_paciente,
                        tra.nombre AS nombre_tratamiento
                    FROM pago p
                    INNER JOIN paciente pac ON p.id_paciente = pac.id_paciente
                    INNER JOIN tratamiento tra ON p.id_tratamiento = tra.id_tratamiento
                    WHERE p.id_pago = :id_pago
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_pago', $id_pago, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error getPagoById: " . $e->getMessage());
            return null;
        }
    }

    
    public function getPagosPorPaciente(int $id_paciente): array
    {
        try {
            $sql = "SELECT 
                        p.id_pago,
                        p.id_paciente,
                        p.id_tratamiento,
                        p.metodo_pago,
                        p.monto,
                        p.estado_pago,
                        p.referencia,
                        p.created_at,
                        tra.nombre AS nombre_tratamiento
                    FROM pago p
                    INNER JOIN tratamiento tra ON p.id_tratamiento = tra.id_tratamiento
                    WHERE p.id_paciente = :id_paciente
                    ORDER BY p.created_at DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPagosPorPaciente: " . $e->getMessage());
            return [];
        }
    }

    
    public function agregar(
        int $id_paciente,
        int $id_tratamiento,
        string $metodo_pago,
        float $monto,
        string $estado_pago,
        ?string $referencia
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO pago (
                        id_paciente,
                        id_tratamiento,
                        metodo_pago,
                        monto,
                        estado_pago,
                        referencia
                    ) VALUES (
                        :id_paciente,
                        :id_tratamiento,
                        :metodo_pago,
                        :monto,
                        :estado_pago,
                        :referencia
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_paciente',  $id_paciente);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento);
            $stmt->bindValue(':metodo_pago', $metodo_pago);
            $stmt->bindValue(':monto', $monto);
            $stmt->bindValue(':estado_pago', $estado_pago);
            $stmt->bindValue(':referencia', $referencia ?: null);

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregar pago: " . $e->getMessage());
            return false;
        }
    }

    
    public function actualizar(
        int $id_pago,
        int $id_paciente,
        int $id_tratamiento,
        string $metodo_pago,
        float $monto,
        string $estado_pago,
        ?string $referencia
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "UPDATE pago
                    SET id_paciente   = :id_paciente,
                        id_tratamiento = :id_tratamiento,
                        metodo_pago    = :metodo_pago,
                        monto          = :monto,
                        estado_pago    = :estado_pago,
                        referencia     = :referencia
                    WHERE id_pago = :id_pago";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(':id_pago', $id_pago);
            $stmt->bindValue(':id_paciente', $id_paciente);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento);
            $stmt->bindValue(':metodo_pago', $metodo_pago);
            $stmt->bindValue(':monto', $monto);
            $stmt->bindValue(':estado_pago', $estado_pago);
            $stmt->bindValue(':referencia', $referencia ?: null);

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar pago: " . $e->getMessage());
            return false;
        }
    }

    
    public function eliminar(int $id_pago): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "DELETE FROM pago
                    WHERE id_pago = :id_pago";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_pago', $id_pago);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar pago: " . $e->getMessage());
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
}
