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

    /**
     * Obtiene todos los pagos con información relacionada
     */
    public function getPagos(): array
    {
        try {
            $sql = "SELECT 
                        pg.id_pago,
                        pg.id_paciente,
                        pg.id_tratamiento,
                        pg.fecha_pago,
                        pg.metodo_pago,
                        pg.monto,
                        pg.estado_pago,
                        pg.referencia,
                        pg.created_at,
                        p.nombre AS nombre_paciente,
                        p.correo AS correo_paciente,
                        t.nombre_tratamiento,
                        t.fecha_inicio,
                        t.fecha_fin,
                        t.precio_unitario,
                        t.cantidad,
                        t.subtotal,
                        o.nombre AS nombre_odontologo
                    FROM pago pg
                    INNER JOIN paciente p ON pg.id_paciente = p.id_paciente
                    INNER JOIN tratamiento t ON pg.id_tratamiento = t.id_tratamiento
                    INNER JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    ORDER BY pg.fecha_pago DESC, pg.created_at DESC";

            return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPagos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un pago por su ID
     */
    public function getPagoById(int $id_pago): ?array
    {
        try {
            $sql = "SELECT 
                        pg.id_pago,
                        pg.id_paciente,
                        pg.id_tratamiento,
                        pg.fecha_pago,
                        pg.metodo_pago,
                        pg.monto,
                        pg.estado_pago,
                        pg.referencia,
                        pg.created_at,
                        p.nombre AS nombre_paciente,
                        p.correo AS correo_paciente,
                        t.nombre_tratamiento,
                        t.fecha_inicio,
                        t.fecha_fin,
                        t.precio_unitario,
                        t.cantidad,
                        t.subtotal,
                        o.nombre AS nombre_odontologo
                    FROM pago pg
                    INNER JOIN paciente p ON pg.id_paciente = p.id_paciente
                    INNER JOIN tratamiento t ON pg.id_tratamiento = t.id_tratamiento
                    INNER JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    WHERE pg.id_pago = :id_pago";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_pago', $id_pago, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error getPagoById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene todos los pagos de un paciente específico
     */
    public function getPagosByPaciente(int $id_paciente): array
    {
        try {
            $sql = "SELECT 
                        pg.id_pago,
                        pg.id_tratamiento,
                        pg.fecha_pago,
                        pg.metodo_pago,
                        pg.monto,
                        pg.estado_pago,
                        pg.referencia,
                        pg.created_at,
                        t.nombre_tratamiento,
                        o.nombre AS nombre_odontologo
                    FROM pago pg
                    INNER JOIN tratamiento t ON pg.id_tratamiento = t.id_tratamiento
                    INNER JOIN odontologo o ON t.id_odontologo = o.id_odontologo
                    WHERE pg.id_paciente = :id_paciente
                    ORDER BY pg.fecha_pago DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPagosByPaciente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los pagos de un tratamiento específico
     */
    public function getPagosByTratamiento(int $id_tratamiento): array
    {
        try {
            $sql = "SELECT 
                        pg.id_pago,
                        pg.fecha_pago,
                        pg.metodo_pago,
                        pg.monto,
                        pg.estado_pago,
                        pg.referencia,
                        pg.created_at,
                        p.nombre AS nombre_paciente,
                        p.correo AS correo_paciente
                    FROM pago pg
                    INNER JOIN paciente p ON pg.id_paciente = p.id_paciente
                    WHERE pg.id_tratamiento = :id_tratamiento
                    ORDER BY pg.fecha_pago DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPagosByTratamiento: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcula el total pagado de un tratamiento
     */
    public function getTotalPagadoTratamiento(int $id_tratamiento): float
    {
        try {
            $sql = "SELECT COALESCE(SUM(monto), 0) as total
                    FROM pago
                    WHERE id_tratamiento = :id_tratamiento
                    AND estado_pago = 'completado'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento, PDO::PARAM_INT);
            $stmt->execute();

            return (float) $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log("Error getTotalPagadoTratamiento: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Agrega un nuevo pago
     */
    public function agregar(
        int $id_paciente,
        int $id_tratamiento,
        string $fecha_pago,
        string $metodo_pago,
        float $monto,
        string $estado_pago,
        ?string $referencia = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO pago (
                        id_paciente,
                        id_tratamiento,
                        fecha_pago,
                        metodo_pago,
                        monto,
                        estado_pago,
                        referencia
                    ) VALUES (
                        :id_paciente,
                        :id_tratamiento,
                        :fecha_pago,
                        :metodo_pago,
                        :monto,
                        :estado_pago,
                        :referencia
                    )";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_pago', $fecha_pago);
            $stmt->bindValue(':metodo_pago', $metodo_pago);
            $stmt->bindValue(':monto', $monto);
            $stmt->bindValue(':estado_pago', $estado_pago);
            $stmt->bindValue(':referencia', $referencia);

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error agregar pago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un pago existente
     */
    public function actualizar(
        int $id_pago,
        int $id_paciente,
        int $id_tratamiento,
        string $fecha_pago,
        string $metodo_pago,
        float $monto,
        string $estado_pago,
        ?string $referencia = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "UPDATE pago SET
                        id_paciente = :id_paciente,
                        id_tratamiento = :id_tratamiento,
                        fecha_pago = :fecha_pago,
                        metodo_pago = :metodo_pago,
                        monto = :monto,
                        estado_pago = :estado_pago,
                        referencia = :referencia
                    WHERE id_pago = :id_pago";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_pago', $id_pago, PDO::PARAM_INT);
            $stmt->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $stmt->bindValue(':id_tratamiento', $id_tratamiento, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_pago', $fecha_pago);
            $stmt->bindValue(':metodo_pago', $metodo_pago);
            $stmt->bindValue(':monto', $monto);
            $stmt->bindValue(':estado_pago', $estado_pago);
            $stmt->bindValue(':referencia', $referencia);

            $stmt->execute();
            $this->conexion->commit();

            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar pago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un pago
     */
    public function eliminar(int $id_pago): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "DELETE FROM pago WHERE id_pago = :id_pago";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_pago', $id_pago, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar pago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si existe un pago con una referencia específica
     */
    public function existeReferencia(string $referencia): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM pago WHERE referencia = :referencia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':referencia', $referencia);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (Throwable $e) {
            error_log("Error existeReferencia: " . $e->getMessage());
            return false;
        }
    }
}