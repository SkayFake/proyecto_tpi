<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class ExpedienteModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    /**
     * Obtener expediente completo de un paciente por nombre
     * (USANDO LIKE)
     */
    public function obtenerExpedientePorNombre(string $nombrePaciente): array
    {
        try {

            $sql = "SELECT 
                        p.nombre AS nombre_paciente1,
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
                        p.nombre LIKE :nombre";

            $stmt = $this->conexion->prepare($sql);

            // IMPORTANTE: agregar % para búsqueda parcial
            $busqueda = '%' . $nombrePaciente . '%';

            $stmt->bindValue(':nombre', $busqueda, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Throwable $e) {
            error_log("Error obtenerExpedientePorNombre: " . $e->getMessage());
            return [];
        }
    }

}
