<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/tratamientoModel.php';
require_once __DIR__ . '/../models/odontologoModel.php';
require_once __DIR__ . '/../models/servicioModel.php';


$tratamientoModel = new Tratamiento();
$odontologoModel  = new Odontologo();
$servicioModel    = new Servicio();

$opcion = $_GET['opcion'] ?? null;

function errorJson(string $msg)
{
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}


function validarDatosTratamientoBase(
    $correo_paciente,
    $id_odontologo,
    $id_servicio,
    $fecha_fin,
    $estado
) {
    if (!filter_var($correo_paciente, FILTER_VALIDATE_EMAIL)) {
        return "El correo del paciente no es válido.";
    }

    if (!$id_odontologo) {
        return "Debe seleccionar un odontólogo.";
    }

    if (!$id_servicio) {
        return "Debe seleccionar un servicio o tratamiento.";
    }

    if ($estado === '' || $estado === null) {
        return "Debe seleccionar el estado del tratamiento.";
    }

    // La fecha_fin puede ser opcional, pero si viene, validar formato de fecha
    if ($fecha_fin !== '' && $fecha_fin !== null) {
        $soloFecha = substr($fecha_fin, 0, 10); 
        $fechaObj  = DateTime::createFromFormat('Y-m-d', $soloFecha);

        if (!$fechaObj) {
            return "Formato de fecha de finalización inválido.";
        }
    }

    return true;
}

try {
    switch ($opcion) {

        
        case 'listar':
            $rows = $tratamientoModel->getTratamientos();
            error_log("Tratamientos recuperados: " . json_encode($rows));
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

      
        case 'listar_odontologos':
            echo json_encode([
                'status' => 'success',
                'data'   => $odontologoModel->getOdontologosSelect()
            ]);
            exit;

        case 'listar_servicios':
            echo json_encode([
                'status' => 'success',
                'data'   => $servicioModel->getServicios()
            ]);
            exit;

       
        case 'buscar_paciente_correo':
            $correo = trim($_GET['correo'] ?? '');
            if ($correo === '') errorJson("Debe ingresar un correo.");

            $paciente = $tratamientoModel->buscarPacientePorCorreo($correo);

            

            if (!$paciente) {
                errorJson("No se encontró un paciente con ese correo.");
            }

            echo json_encode(['status' => 'success', 'data' => $paciente]);
            exit;

        
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $row = $tratamientoModel->getTratamientoById($id);
            if (!$row) errorJson("Tratamiento no encontrado.");

            echo json_encode(['status' => 'success', 'data' => $row]);
            exit;

        case 'tratamientos_paciente':
            $id_paciente = intval($_GET['id_paciente'] ?? 0);
            if (!$id_paciente) errorJson("ID de paciente inválido.");

            $rows = $tratamientoModel->getTratamientosPorPaciente($id_paciente);

            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

    
        case 'agregar':
            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $id_servicio     = intval($_POST['id_servicio'] ?? 0);
            $fecha_fin       = trim($_POST['fecha_fin'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            $valid = validarDatosTratamientoBase(
                $correo_paciente,
                $id_odontologo,
                $id_servicio,
                $fecha_fin,
                $estado
            );
            if ($valid !== true) errorJson($valid);

            // Buscar paciente por correo
            $paciente = $tratamientoModel->buscarPacientePorCorreo($correo_paciente);
            if (!$paciente) {
                errorJson("No existe un paciente con ese correo.");
            }

            $id_paciente = (int)$paciente['id_paciente'];

            $ok = $tratamientoModel->agregar(
                $id_paciente,
                $id_odontologo,
                $id_servicio,
                $fecha_fin !== '' ? $fecha_fin : null,
                $estado,
                $notas !== '' ? $notas : null
            );

            if (!$ok) errorJson("No se pudo crear el tratamiento.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Tratamiento creado exitosamente'
            ]);
            exit;

  
        case 'actualizar':
            $id_tratamiento  = intval($_POST['id_tratamiento'] ?? 0);
            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $id_servicio     = intval($_POST['id_servicio'] ?? 0);
            $fecha_fin       = trim($_POST['fecha_fin'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            if (!$id_tratamiento) errorJson("ID inválido.");

            $tratamiento = $tratamientoModel->getTratamientoById($id_tratamiento);
            if (!$tratamiento) errorJson("Tratamiento no encontrado.");

            $valid = validarDatosTratamientoBase(
                $correo_paciente,
                $id_odontologo,
                $id_servicio,
                $fecha_fin,
                $estado
            );
            if ($valid !== true) errorJson($valid);

            
            $id_paciente = (int)$tratamiento['id_paciente'];

            $ok = $tratamientoModel->actualizar(
                $id_tratamiento,
                $id_paciente,
                $id_odontologo,
                $id_servicio,
                $fecha_fin !== '' ? $fecha_fin : null,
                $estado,
                $notas !== '' ? $notas : null
            );

            if (!$ok) errorJson("No se pudo actualizar el tratamiento.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Tratamiento actualizado correctamente'
            ]);
            exit;

        case 'eliminar':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $ok = $tratamientoModel->eliminar($id);
            if (!$ok) errorJson("No se pudo eliminar el tratamiento.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Tratamiento eliminado'
            ]);
            exit;

        default:
            errorJson("Opción no válida.");
    }
} catch (Throwable $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
