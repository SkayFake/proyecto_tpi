<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Paciente {

    private PDO $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function agregar(
        string $nombre,
        ?string $fechaNac,
        ?string $sexo,
        ?string $telefono,
        ?string $correo,
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
                        :correo,
                        :direccion,
                        :dui,
                        :notas
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
            $stmt->bindValue(":fecha_nacimiento", $fechaNac, $fechaNac ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":sexo", $sexo, $sexo ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":telefono", $telefono, $telefono ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":correo", $correo, $correo ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":direccion", $direccion, $direccion ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":dui", $dui, $dui ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":notas", $notas, $notas ? PDO::PARAM_STR : PDO::PARAM_NULL);

            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error registrar paciente: " . $e->getMessage());
            throw $e;
        }
    }


    public function getPacientes(): array {
        try {
            $sql = "SELECT 
                        id_paciente, 
                        nombre, 
                        fecha_nacimiento, 
                        sexo, 
                        telefono, 
                        correo,
                        direccion, 
                        dui, 
                        notas 
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
            $sql = "SELECT 
                        id_paciente, 
                        nombre, 
                        fecha_nacimiento, 
                        sexo, 
                        telefono, 
                        correo,
                        direccion, 
                        dui, 
                        notas 
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
        ?string $telefono,
        ?string $correo,
        ?string $direccion,
        ?string $dui,
        ?string $notas
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
                        :correo,
                        :direccion,
                        :dui,
                        :notas
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_paciente", $id_paciente, PDO::PARAM_INT);
            $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
            $stmt->bindValue(":fecha_nacimiento", $fecha_nacimiento, $fecha_nacimiento ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":sexo", $sexo, $sexo ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":telefono", $telefono, $telefono ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":correo", $correo, $correo ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":direccion", $direccion, $direccion ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":dui", $dui, $dui ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":notas", $notas, $notas ? PDO::PARAM_STR : PDO::PARAM_NULL);

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
