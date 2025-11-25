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

    // Conversión correcta de fecha
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    $hoyObj   = new DateTime('today');

    if (!$fechaObj) {
        return "Formato de fecha inválido.";
    }

    if ($fechaObj <= $hoyObj) {
        return "La fecha debe ser mayor a hoy.";
    }

    // Domingo = 0
    if ((int)$fechaObj->format('w') === 0) {
        return "No se permiten citas los domingos.";
    }

    if (!$hora) {
        return "Debe seleccionar una hora válida.";
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

        /* ==========================================================
            LISTAR CITAS
        ========================================================== */
        case 'listar':
            $rows = $citaModel->getCitas();
            $response = ['status' => 'success', 'data' => $rows];
            break;


        /* ==========================================================
            LISTAR ODONTÓLOGOS
        ========================================================== */
        case 'listar_odontologos':
            $rows = $odontologoModel->getOdontologosSelect();
            $response = ['status' => 'success', 'data' => $rows];
            break;


        /* ==========================================================
            BUSCAR PACIENTE POR CORREO
        ========================================================== */
        case 'buscar_paciente_correo':

            $correo = trim($_GET['correo'] ?? '');

            if ($correo === '') {
                errorJson("Debe ingresar un correo.");
            }

            $paciente = $citaModel->buscarPacientePorCorreo($correo);

            if (!$paciente) {
                errorJson("No se encontró un paciente con ese correo.");
            }

            $response = ['status' => 'success', 'data' => $paciente];
            break;


        /* ==========================================================
            OBTENER CITA POR ID
        ========================================================== */
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);

            if (!$id) errorJson("ID inválido.");

            $row = $citaModel->getCitaById($id);

            if (!$row) errorJson("Cita no encontrada.");

            $response = ['status' => 'success', 'data' => $row];
            break;



        /* ==========================================================
            AGREGAR CITA
        ========================================================== */
        case 'agregar':

            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $fecha_cita      = trim($_POST['fecha_cita'] ?? '');
            $hora_cita       = trim($_POST['hora_cita'] ?? '');
            $motivo          = trim($_POST['motivo'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');

            // Validación general
            $valid = validarDatosCitaBase($correo_paciente, $id_odontologo, $fecha_cita, $hora_cita, $motivo, $estado);
            if ($valid !== true) errorJson($valid);

            // Buscar paciente
            $paciente = $citaModel->buscarPacientePorCorreo($correo_paciente);
            if (!$paciente) errorJson("No existe un paciente con ese correo.");

            $id_paciente = $paciente['id_paciente'];

            // Validar duplicado
            if ($citaModel->existeCita($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("Ya existe una cita con ese odontólogo en ese horario.");
            }

            // Validar disponibilidad
            if (!$citaModel->hayDisponibilidad($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("El odontólogo no tiene disponibilidad en ese horario.");
            }

            // Crear cita
            $ok = $citaModel->agregar($id_paciente, $id_odontologo, $fecha_cita, $hora_cita, $motivo, $estado);

            if (!$ok) errorJson("No se pudo crear la cita.");

            $response = ['status' => 'success', 'message' => 'Cita creada exitosamente'];
            break;



        /* ==========================================================
            ACTUALIZAR CITA
        ========================================================== */
        case 'actualizar':

            $id_cita         = intval($_POST['id_cita'] ?? 0);
            $correo_paciente = trim($_POST['correo_paciente'] ?? '');
            $id_odontologo   = intval($_POST['id_odontologo'] ?? 0);
            $fecha_cita      = trim($_POST['fecha_cita'] ?? '');
            $hora_cita       = trim($_POST['hora_cita'] ?? '');
            $motivo          = trim($_POST['motivo'] ?? '');
            $estado          = trim($_POST['estado'] ?? '');

            if (!$id_cita) errorJson("ID inválido.");

            // Obtener cita actual
            $cita = $citaModel->getCitaById($id_cita);
            if (!$cita) errorJson("Cita no encontrada.");

            // No permitir editar citas atendidas
            if ($cita['estado'] === 'atendida') {
                errorJson("No se puede editar una cita que ya fue atendida.");
            }

            // Validación general
            $valid = validarDatosCitaBase($correo_paciente, $id_odontologo, $fecha_cita, $hora_cita, $motivo, $estado);
            if ($valid !== true) errorJson($valid);

            // Paciente NO se puede cambiar
            $id_paciente = $cita['id_paciente'];

            // Duplicado EXCEPTO esta misma cita
            if ($citaModel->existeOtraCita($id_cita, $id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("Ya existe otra cita con ese odontólogo en ese horario.");
            }

            // Validar disponibilidad
            if (!$citaModel->hayDisponibilidad($id_odontologo, $fecha_cita, $hora_cita)) {
                errorJson("El odontólogo no tiene disponibilidad en ese horario.");
            }

            // Actualizar
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

            $response = ['status' => 'success', 'message' => 'Cita actualizada correctamente'];
            break;



        /* ==========================================================
            ELIMINAR CITA
        ========================================================== */
        case 'eliminar':

            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $ok = $citaModel->eliminar($id);

            if (!$ok) errorJson("No se pudo eliminar.");

            $response = ['status' => 'success', 'message' => 'Cita eliminada'];
            break;
    }

} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
