<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/citaModel.php';
require_once __DIR__ . '/../models/odontologoModel.php';

$citaModel       = new Cita();
$odontologoModel = new Odontologo();

$opcion = $_GET['opcion'] ?? null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

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
                $response = ['status' => 'error', 'message' => 'Debe ingresar un correo'];
                break;
            }

            $paciente = $citaModel->buscarPacientePorCorreo($correo);

            if (!$paciente) {
                $response = [
                    'status' => 'error',
                    'message' => 'No se encontró un paciente con ese correo'
                ];
            } else {
                $response = ['status' => 'success', 'data' => $paciente];
            }
            break;


        /* ==========================================================
            OBTENER CITA POR ID
        ========================================================== */
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);

            if (!$id) {
                $response = ['status' => 'error', 'message' => 'ID inválido'];
                break;
            }

            $row = $citaModel->getCitaById($id);

            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Cita no encontrada'];
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

            if (preg_match('/^\d{2}:\d{2}$/', $hora_cita)) {
                $hora_cita .= ':00';
            }

            if (!$correo_paciente || !$id_odontologo || !$fecha_cita || !$hora_cita || !$estado) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Correo, odontólogo, fecha, hora y estado son obligatorios'
                ];
                break;
            }

            $paciente = $citaModel->buscarPacientePorCorreo($correo_paciente);

            if (!$paciente) {
                $response = [
                    'status'  => 'error',
                    'message' => 'No existe un paciente con ese correo'
                ];
                break;
            }

            $id_paciente = $paciente['id_paciente'];

            $ok = $citaModel->agregar(
                $id_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo ?: null,
                $estado
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita creada exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo crear la cita'];
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

            if (preg_match('/^\d{2}:\d{2}$/', $hora_cita)) {
                $hora_cita .= ':00';
            }

            if (!$id_cita || !$correo_paciente || !$id_odontologo || !$fecha_cita || !$hora_cita || !$estado) {
                $response = ['status' => 'error', 'message' => 'Datos incompletos'];
                break;
            }

            $paciente = $citaModel->buscarPacientePorCorreo($correo_paciente);

            if (!$paciente) {
                $response = [
                    'status'  => 'error',
                    'message' => 'No existe un paciente con ese correo'
                ];
                break;
            }

            $id_paciente = $paciente['id_paciente'];

            $ok = $citaModel->actualizar(
                $id_cita,
                $id_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo ?: null,
                $estado
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita actualizada']
                : ['status' => 'error', 'message' => 'No se pudo actualizar'];
            break;



        /* ==========================================================
            ELIMINAR CITA
        ========================================================== */
        case 'eliminar':

            $id = intval($_POST['id'] ?? 0);

            if (!$id) {
                $response = ['status' => 'error', 'message' => 'ID inválido'];
                break;
            }

            $ok = $citaModel->eliminar($id);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita eliminada']
                : ['status' => 'error', 'message' => 'No se pudo eliminar'];
            break;
    }

} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
