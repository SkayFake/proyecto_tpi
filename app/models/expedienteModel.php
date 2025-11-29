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
                        o.imagen AS odontograma_imagen_blob,
                        o.observaciones AS odontograma_observaciones,
                        o.created_at AS odontograma_fecha,
                        s.nombre AS tratamiento_nombre,
                        t.estado AS tratamiento_estado,
                        t.notas AS tratamiento_notas
                    FROM paciente p
                    LEFT JOIN odontograma o ON o.id_paciente = p.id_paciente
                    LEFT JOIN tratamiento t ON t.id_paciente = p.id_paciente
                    LEFT JOIN servicio s ON s.id_servicio = t.id_servicio
                    WHERE p.nombre LIKE :nombre
                    ORDER BY o.created_at DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', '%' . $nombrePaciente . '%', PDO::PARAM_STR);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                if (!empty($row['odontograma_imagen_blob'])) {
                    $blob = $row['odontograma_imagen_blob'];

                    if (strpos($blob, "\x89PNG") === 0) {
                        $mime = "image/png";
                    } elseif (strpos($blob, "\xFF\xD8\xFF") === 0) {
                        $mime = "image/jpeg";
                    } else {
                        $mime = "image/jpeg"; 
                    }

                    $row['odontograma_imagen'] = "data:$mime;base64," . base64_encode($blob);
                } else {
                    $row['odontograma_imagen'] = null;
                }
            }

            return $data;

        } catch (Throwable $e) {
            error_log("Error obtenerExpedientePorNombre: " . $e->getMessage());
            return [];
        }
    }
}
