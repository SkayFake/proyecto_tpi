<?php


declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Expediente
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    /**
     * Obtener expediente completo de un paciente por su nombre
     * @param string $nombrePaciente
     * @return array
     */
    public function obtenerExpedientePorNombre(string $nombrePaciente): array
    {
        try {
            $sql = "SELECT 
                        p.nombre AS paciente_nombre,
                        p.fecha_nacimiento,
                        p.sexo,
                        p.telefono,
                        p.correo,
                        p.direccion,
                        p.dui,
                        p.notas AS paciente_notas,
                        o.imagen AS odontograma_imagen,
                        o.observaciones AS odontograma_observaciones,
                        o.created_at AS odontograma_fecha,
                        s.nombre AS tratamiento_nombre,
                        t.estado AS tratamiento_estado,
                        t.notas AS tratamiento_notas
                    FROM 
                        paciente p
                    LEFT JOIN 
                        odontograma o ON o.id_paciente = p.id_paciente
                    LEFT JOIN 
                        tratamiento t ON t.id_paciente = p.id_paciente
                    LEFT JOIN 
                        servicio s ON s.id_servicio = t.id_servicio
                    WHERE 
                        p.nombre = :nombre";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', $nombrePaciente, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Throwable $e) {
            error_log("Error obtenerExpedientePorNombre: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar todos los pacientes para el select
     * @return array
     */
    public function listarPacientes(): array
    {
        try {
            $sql = "SELECT DISTINCT id_paciente, nombre 
                    FROM paciente 
                    ORDER BY nombre";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Throwable $e) {
            error_log("Error listarPacientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar pacientes por término de búsqueda
     * @param string $termino
     * @return array
     */
    public function buscarPacientes(string $termino): array
    {
        try {
            $sql = "SELECT DISTINCT id_paciente, nombre 
                    FROM paciente 
                    WHERE nombre LIKE :termino 
                    ORDER BY nombre
                    LIMIT 10";
            
            $stmt = $this->conexion->prepare($sql);
            $busqueda = "%{$termino}%";
            $stmt->bindValue(':termino', $busqueda, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Throwable $e) {
            error_log("Error buscarPacientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si existe un paciente por nombre exacto
     * @param string $nombre
     * @return bool
     */
    public function existePaciente(string $nombre): bool
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM paciente 
                    WHERE nombre = :nombre";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'] > 0;
            
        } catch (Throwable $e) {
            error_log("Error existePaciente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener datos básicos del paciente
     * @param string $nombre
     * @return array|null
     */
    public function obtenerDatosPaciente(string $nombre): ?array
    {
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
                    WHERE nombre = :nombre";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->execute();
            
            $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $paciente ?: null;
            
        } catch (Throwable $e) {
            error_log("Error obtenerDatosPaciente: " . $e->getMessage());
            return null;
        }
    }
}