<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/citaModel.php';
require_once __DIR__ . '/../models/odontologoModel.php';

$citaModel       = new Cita();
$odontologoModel = new Odontologo();

$opcion = $_GET['opcion'] ?? null;

function errorJson(string $msg)
{
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

function validarDatosCitaBase($correo, $id_odontologo, $fecha, $hora, $motivo, $estado)
{
     error_log("Correo: " . $correo);
    error_log("Odontólogo: " . $id_odontologo);
    error_log("Fecha Cita: " . $fecha);
    error_log("Hora Cita: " . $hora);
    error_log("Motivo: " . $motivo);
    error_log("Estado: " . $estado);
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return "El correo del paciente no es válido.";
    }

    if (!$id_odontologo) {
        return "Debe seleccionar un odontólogo.";
    }

    if (trim($motivo) === '') {
        return "Debe escribir el motivo de la cita.";
    }

    if (!$fecha) {
        return "Debe seleccionar una fecha.";
    }

    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    $hoyObj   = new DateTime('today');

    if (!$fechaObj) {
        return "Formato de fecha inválido.";
    }

    if ($fechaObj <= $hoyObj) {
        return "La fecha debe ser mayor a hoy.";
    }

    if ((int)$fechaObj->format('w') === 0) {
        return "No se permiten citas los domingos.";
    }

    if (!$hora) {
        return "Debe seleccionar una hora válida.";
    }

   
$minutos = intval(substr($hora, 3, 2));
if ($minutos !== 0) {
    return "Las citas solo pueden crearse en horas exactas (ejemplo: 09:00, 10:00).";
}


    $h = intval(substr($hora, 0, 2));
    if ($h < 7 || $h > 17) {
        return "La hora debe estar entre 07:00 y 17:00.";
    }

    if (!$estado) {
        return "Debe seleccionar el estado de la cita.";
    }

    return true;
}


try {

    switch ($opcion) {

        
        case 'listar':
            $citaModel->autoActualizarCitasAtendidas();
            $rows = $citaModel->getCitas();
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

        case 'listar_odontologos':
            echo json_encode([
                'status' => 'success',
                'data' => $odontologoModel->getOdontologosSelect()
            ]);
            exit;

        case 'buscar_paciente_correo':

            $correo = trim($_GET['correo'] ?? '');
            if ($correo === '') errorJson("Debe ingresar un correo.");

            $paciente = $citaModel->buscarPacientePorCorreo($correo);
            if (!$paciente) errorJson("No se encontró un paciente con ese correo.");

            echo json_encode(['status' => 'success', 'data' => $paciente]);
            exit;
        case 'obtener':

            $id = intval($_GET['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $citaModel->autoActualizarCitasAtendidas();
            $row = $citaModel->getCitaById($id);
            if (!$row) errorJson("Cita no encontrada.");

            echo json_encode(['status' => 'success', 'data' => $row]);
            exit;

        case 'agregar':

            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $fecha_cita      = trim($_POST['fecha_cita'] ?? '');
            $hora_cita       = trim($_POST['hora_cita'] ?? '');
            $motivo          = trim($_POST['motivo'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');

            $valid = validarDatosCitaBase($correo_paciente, $id_odontologo, $fecha_cita, $hora_cita, $motivo, $estado);
            if ($valid !== true) errorJson($valid);

            $paciente = $citaModel->buscarPacientePorCorreo($correo_paciente);
            if (!$paciente) errorJson("No existe un paciente con ese correo.");

            $id_paciente = $paciente['id_paciente'];

            if ($citaModel->existeCita($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("Ya existe una cita con ese odontólogo en ese horario.");
            }

            if (!$citaModel->hayDisponibilidad($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("El odontólogo no tiene disponibilidad en ese horario.");
            }

            /* GENERAR TOKEN */
            $token = bin2hex(random_bytes(16));

            $ok = $citaModel->agregar(
                $id_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo,
                $estado,
                $token
            );

            if (!$ok) errorJson("No se pudo crear la cita.");

            echo json_encode(['status' => 'success', 'message' => 'Cita creada exitosamente']);
            exit;

        case 'actualizar':

            $id_cita         = intval($_POST['id_cita'] ?? 0);
            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $fecha_cita      = trim($_POST['fecha_cita'] ?? '');
            $hora_cita       = trim($_POST['hora_cita'] ?? '');
            $motivo          = trim($_POST['motivo'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');

            if (!$id_cita) errorJson("ID inválido.");

            $cita = $citaModel->getCitaById($id_cita);
            if (!$cita) errorJson("Cita no encontrada.");

            if ($cita['estado'] === 'atendida') {
                errorJson("No se puede editar una cita que ya fue atendida.");
            }

            $valid = validarDatosCitaBase($correo_paciente, $id_odontologo, $fecha_cita, $hora_cita, $motivo, $estado);
            if ($valid !== true) errorJson($valid);

            $id_paciente = $cita['id_paciente'];

            if ($citaModel->existeOtraCita($id_cita, $id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("Ya existe otra cita con ese odontólogo en ese horario.");
            }

            if (!$citaModel->hayDisponibilidad($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("El odontólogo no tiene disponibilidad en ese horario.");
            }

            $ok = $citaModel->actualizar(
                $id_cita,
                $id_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo,
                $estado
            );

            if (!$ok) errorJson("No se pudo actualizar la cita.");

            echo json_encode(['status' => 'success', 'message' => 'Cita actualizada correctamente']);
            exit;

        case 'eliminar':

            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $ok = $citaModel->eliminar($id);
            if (!$ok) errorJson("No se pudo eliminar.");

            echo json_encode(['status' => 'success', 'message' => 'Cita eliminada']);
            exit;


        case 'validar_disponibilidad':

            $id_odontologo = intval($_GET['id_odontologo'] ?? 0);
            $fecha         = $_GET['fecha_cita'] ?? '';
            $hora          = $_GET['hora_cita'] ?? '';

            if (!$id_odontologo || !$fecha || !$hora) {
                errorJson("Datos incompletos para validar disponibilidad.");
            }

            $rows = $citaModel->getCitas();

            foreach ($rows as $r) {
                if ($r['id_odontologo'] != $id_odontologo) continue;
                if ($r['fecha_cita'] !== $fecha) continue;

                $hNueva = strtotime($hora);
                $hExistente = strtotime($r['hora_cita']);

                if (abs($hNueva - $hExistente) < 3600) {
                    errorJson("Debe existir al menos 1 hora entre citas del mismo odontólogo.");
                }
            }

            echo json_encode(["status" => "success", "message" => "Disponible"]);
            exit;

            case 'horarios_disponibles':

    $id_odontologo = intval($_GET['id_odontologo'] ?? 0);
    $fecha         = $_GET['fecha'] ?? '';

    if (!$id_odontologo || !$fecha) {
        errorJson("Debe seleccionar odontólogo y fecha.");
    }

    // 1. Traer disponibilidad del odontólogo
    require_once __DIR__ . '/../models/disponibilidadModel.php';
    $dis = new Disponibilidad();

    $rangos = $dis->listar(); // tú ya puedes hacer un método específico si gustas

    $horas = [];

    foreach ($rangos as $r) {

        if ($r['id_odontologo'] != $id_odontologo) continue;
        if ($fecha < $r['fecha_inicio'] || $fecha > $r['fecha_fin']) continue;

        // Generar horas
        $inicio = strtotime($r['hora_inicio']);
        $fin    = strtotime($r['hora_fin']);

        for ($h = $inicio; $h < $fin; $h += 3600) {
            $horas[] = date("H:i", $h);
        }
    }

    // 2. Excluir horas ya reservadas
    $citas = $citaModel->getCitas();

    foreach ($citas as $c) {
        if ($c['id_odontologo'] == $id_odontologo && $c['fecha_cita'] == $fecha) {
            $hora = substr($c['hora_cita'], 0, 5);
            if (($key = array_search($hora, $horas)) !== false) {
                unset($horas[$key]);
            }
        }
    }

    echo json_encode([
        "status" => "success",
        "data"   => array_values($horas)
    ]);
    exit;


            case 'horas_disponibles':

    $id_odontologo = intval($_GET['id_odontologo'] ?? 0);
    $fecha         = $_GET['fecha'] ?? '';

    if (!$id_odontologo || !$fecha) {
        errorJson("Faltan datos para obtener horas disponibles.");
    }

    // Obtener disponibilidad del odontólogo
    require_once __DIR__ . '/../models/disponibilidadModel.php';
    $disModel = new Disponibilidad();
    $disp = $disModel->obtenerPorFecha($id_odontologo, $fecha);

    if (!$disp) {
        errorJson("El odontólogo no tiene disponibilidad registrada para esta fecha.");
    }

    // Generar horas completas
    $inicio = strtotime($disp['hora_inicio']);
    $fin    = strtotime($disp['hora_fin']);

    $horas = [];
    for ($t = $inicio; $t < $fin; $t += 3600) {
        $horas[] = date("H:i", $t);
    }

    // Obtener citas ocupadas
    $citas = $citaModel->getCitas();
    $ocupadas = [];

    foreach ($citas as $c) {
        if ($c['id_odontologo'] == $id_odontologo && $c['fecha_cita'] == $fecha) {
            $ocupadas[] = substr($c['hora_cita'], 0, 5);
        }
    }

    // Filtrar
    $libres = array_values(array_diff($horas, $ocupadas));

    echo json_encode([
        "status" => "success",
        "data"   => $libres
    ]);
    exit;



        default:
            errorJson("Opción no válida.");
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

