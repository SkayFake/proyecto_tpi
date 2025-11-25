<?php
declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Odontograma
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function agregar(int $id_paciente, string $imagen, string $observaciones){

        try{
            $this->conexion->beginTransaction();
            $sql = "CALL sp_odontograma_insert(:id_paciente, :imagen, :observaciones)"; 
            $stm = $this->conexion->prepare($sql);

            $stm->bindValue(':id_paciente',$id_paciente,PDO::PARAM_INT);
            if ($imagen !==null)$stm->bindValue(':imagen',$imagen,PDO::PARAM_STR);
            else $stm->bindValue(':imagen',null,PDO::PARAM_NULL);
            $stm->bindValue(':observaciones',$observaciones,PDO::PARAM_STR);
            $stm->execute();
            $this->conexion->commit();
            return true;
        }catch(Throwable $e){
            error_log("Error al agregar odontograma: " . $e->getMessage());
            return false;
        }   
    }

    public function actualizar(int $id_odontograma, int $id_paciente, ?string $imagen, string $observaciones):bool{
    
    try{
        $this->conexion->beginTransaction();
        
        if ($imagen !== null) {
            $sql = "CALL sp_odontograma_update(:id_odontograma, :id_paciente, :imagen, :observaciones)"; 
            $stm = $this->conexion->prepare($sql);
            $stm->bindValue(':id_odontograma',$id_odontograma,PDO::PARAM_INT);
            $stm->bindValue(':id_paciente',$id_paciente,PDO::PARAM_INT);
            $stm->bindValue(':imagen',$imagen,PDO::PARAM_STR);
            $stm->bindValue(':observaciones',$observaciones,PDO::PARAM_STR);
        } else {
        
            $sql = "UPDATE odontograma SET id_paciente=:id_paciente, observaciones=:observaciones WHERE id_odontograma=:id_odontograma"; 
            $stm = $this->conexion->prepare($sql);
            $stm->bindValue(':id_odontograma',$id_odontograma,PDO::PARAM_INT);
            $stm->bindValue(':id_paciente',$id_paciente,PDO::PARAM_INT);
            $stm->bindValue(':observaciones',$observaciones,PDO::PARAM_STR);
        }
        
        $stm->execute();
        $this->conexion->commit();
        return true;
    }catch(Throwable $e){
        $this->conexion->rollBack();
        error_log("Error al actualizar odontograma: " . $e->getMessage());
        return false;
    }
}

    public function eliminar(int $id_odontograma):bool{
        try{
            $this->conexion->beginTransaction();
            $sql = "CALL sp_odontograma_delete(:id_odontograma)"; 
            $stm = $this->conexion->prepare($sql);

            $stm->bindValue(':id_odontograma',$id_odontograma,PDO::PARAM_INT);
            $stm->execute();
            $this->conexion->commit();
            return true;
        }catch(Throwable $e){
            error_log("Error al eliminar odontograma: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerFoto(int $id_odontograma): string{
        try{

            $sql = "SELECT imagen FROM odontograma WHERE id_odontograma=:id_odontograma";
            $stm = $this->conexion->prepare($sql);
            $stm->bindValue(':id_odontograma',$id_odontograma,PDO::PARAM_INT);
            $stm->execute();
            $resultado = $stm->fetch(PDO::FETCH_ASSOC);
            if ($resultado && isset($resultado['imagen'])) {
                return $resultado['imagen'];
            } else {
                return "";
            }
        }catch(Throwable $e){
            error_log("Error al obtener foto del odontograma: " . $e->getMessage());
            return "";
        }
    }
    
    public function obtenerPorId(int $id): ?array
{
    try {
        $sql = "SELECT
                    o.id_odontograma,
                    o.id_paciente,
                    p.nombre AS name_paciente,
                    o.observaciones,
                    CASE 
                        WHEN o.imagen IS NOT NULL AND o.imagen <> '' THEN 1
                        ELSE 0
                    END AS tiene_foto
                FROM odontograma o
                INNER JOIN paciente p ON o.id_paciente = p.id_paciente
                WHERE o.id_odontograma = :id
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;

    } catch (Throwable $e) {
        error_log("Error al obtener odontograma: " . $e->getMessage());
        return null;
    }
}

    public function listar(): array
{
    try {
        $sql = "SELECT 
                    o.id_odontograma,
                    p.nombre AS name_paciente,
                    o.observaciones,
                    CASE 
                        WHEN o.imagen IS NOT NULL AND o.imagen <> '' THEN 1
                        ELSE 0
                    END AS tiene_foto
                FROM odontograma o
                INNER JOIN paciente p ON o.id_paciente = p.id_paciente
                ORDER BY o.id_odontograma DESC";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Throwable $e) {
        error_log('Error al listar odontogramas: ' . $e->getMessage());
        return [];
    }
}


    public function buscarPorNombrePaciente(string $nombre):array{
        try {
           $sql = "SELECT id_paciente,nombre AS name_nombre FROM paciente 
           WHERE nombre = :nombre LIMIT 1";

           $stm = $this->conexion->prepare($sql);
           $stm->bindValue(":nombre", $nombre, PDO::PARAM_STR);
           $stm->execute();
           return $stm->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Throwable $e) {
            error_log("Error al obtener nombre del paciente: " . $e->getMessage());
            return [];
        }
    }
}
