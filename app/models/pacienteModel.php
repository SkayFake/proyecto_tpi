<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Paciente {

    private PDO $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
        // Asegúrate que en Conexion::conectar() tengas:
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function agregar(
        string $nombre,
        ?string $fechaNac,
        ?string $sexo,
        ?string $telefono,
        ?string $direccion,
        ?string $dui,
        ?string $notas
    ): bool {
        try {
            $this->conexion->beginTransaction();

            if ($fechaNac) {
                $fechaNac = date('Y-m-d', strtotime($fechaNac));
            }

            $sql = "CALL sp_paciente_insert(
                        :nombre,
                        :fecha_nacimiento,
                        :sexo,
                        :telefono,
                        :direccion,
                        :dui,
                        :notas
                    )";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);

            if ($fechaNac === null) {
                $stmt->bindValue(":fecha_nacimiento", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":fecha_nacimiento", $fechaNac, PDO::PARAM_STR);
            }

            if ($sexo === null) {
                $stmt->bindValue(":sexo", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":sexo", $sexo, PDO::PARAM_STR);
            }

            if ($telefono === null) {
                $stmt->bindValue(":telefono", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":telefono", $telefono, PDO::PARAM_STR);
            }

            if ($direccion === null) {
                $stmt->bindValue(":direccion", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":direccion", $direccion, PDO::PARAM_STR);
            }

            if ($dui === null) {
                $stmt->bindValue(":dui", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":dui", $dui, PDO::PARAM_STR);
            }

            if ($notas === null) {
                $stmt->bindValue(":notas", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":notas", $notas, PDO::PARAM_STR);
            }

            $stmt->execute();
            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error registrar paciente: " . $e->getMessage());
            // Re-lanzamos la excepción para que el controller la capture
            throw $e;
        }
    }

    public function getPacientes(): array {
        try {
            $sql = "SELECT id_paciente, nombre, fecha_nacimiento, sexo, telefono, direccion, dui, notas 
                    FROM paciente";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error getPacientes: " . $e->getMessage());
            return [];
        }
    }

    public function getPacienteById(int $id_paciente): ?array {
        try {
            $sql = "SELECT id_paciente, nombre, fecha_nacimiento, sexo, telefono, direccion, dui, notas 
                    FROM paciente 
                    WHERE id_paciente = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id", $id_paciente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error getPacienteById: " . $e->getMessage());
            return null;
        }
    }

    public function actualizar(
        int $id_paciente,
        string $nombre,
        ?string $fecha_nacimiento,
        ?string $sexo,
        ?string $telefono = null,
        ?string $direccion = null,
        ?string $dui = null,
        ?string $notas = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            if ($fecha_nacimiento) {
                $fecha_nacimiento = date('Y-m-d', strtotime($fecha_nacimiento));
            }

            $sql = "CALL sp_paciente_update(
                        :id_paciente,
                        :nombre,
                        :fecha_nacimiento,
                        :sexo,
                        :telefono,
                        :direccion,
                        :dui,
                        :notas
                    )";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_paciente", $id_paciente, PDO::PARAM_INT);
            $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);

            if ($fecha_nacimiento === null) {
                $stmt->bindValue(":fecha_nacimiento", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":fecha_nacimiento", $fecha_nacimiento, PDO::PARAM_STR);
            }

            if ($sexo === null) {
                $stmt->bindValue(":sexo", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":sexo", $sexo, PDO::PARAM_STR);
            }

            if ($telefono === null) {
                $stmt->bindValue(":telefono", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":telefono", $telefono, PDO::PARAM_STR);
            }

            if ($direccion === null) {
                $stmt->bindValue(":direccion", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":direccion", $direccion, PDO::PARAM_STR);
            }

            if ($dui === null) {
                $stmt->bindValue(":dui", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":dui", $dui, PDO::PARAM_STR);
            }

            if ($notas === null) {
                $stmt->bindValue(":notas", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":notas", $notas, PDO::PARAM_STR);
            }

            $stmt->execute();
            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar paciente: " . $e->getMessage());
            throw $e;
        }
    }

    public function eliminar(int $id_paciente): bool {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_paciente_delete(:id_paciente)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id_paciente", $id_paciente, PDO::PARAM_INT);
            $stmt->execute();

            $this->conexion->commit();
            return true;

        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar paciente: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
