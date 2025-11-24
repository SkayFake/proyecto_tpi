<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/citaModel.php';
require_once __DIR__ . '/../models/odontologoModel.php';

$citaModel       = new Cita();
$odontologoModel = new Odontologo();

$opcion = isset($_GET['opcion']) ? trim($_GET['opcion']) : null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

try {
    switch ($opcion) {

        case 'listar':
            $rows = $citaModel->getCitas();
            $response = ['status' => 'success', 'data' => $rows];
            break;

        case 'listar_odontologos':
            $rows = $odontologoModel->getOdontologosSelect();

            $response = ['status' => 'success', 'data' => $rows];
            break;

        case 'obtener':
            $id = (int)($_GET['id'] ?? 0);

            if (!$id) {
                $response = ['status' => 'error', 'message' => 'ID de cita inválido'];
                break;
            }

            $row = $citaModel->getCitaById($id);

            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Cita no encontrada'];
            break;
        case 'agregar':
            $dui_paciente  = trim($_POST['dui'] ?? '');
            $id_odontologo = (int)($_POST['id_odontologo'] ?? 0);
            $fecha_cita    = trim($_POST['fecha_cita'] ?? '');
            $hora_cita     = trim($_POST['hora_cita'] ?? '');
            $motivo        = trim($_POST['motivo'] ?? '');
            $estado        = trim($_POST['estado'] ?? 'programada');

            if (preg_match('/^\d{2}:\d{2}$/', $hora_cita)) {
                $hora_cita .= ':00';
            }

            $estadosValidos = ['programada', 'confirmada', 'atendida', 'cancelada'];
            if ($estado === '' || !in_array($estado, $estadosValidos, true)) {
                $estado = 'programada';
            }

            if (!$dui_paciente || !$id_odontologo || !$fecha_cita || !$hora_cita) {
                $response = ['status' => 'error', 'message' => 'DUI, odontólogo, fecha y hora son obligatorios'];
                break;
            }

            $ok = $citaModel->agregarPorDui(
                $dui_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo !== '' ? $motivo : null,
                $estado
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita creada exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo crear la cita. Verifique el DUI y la disponibilidad.'];
            break;

        case 'actualizar':
            $id_cita       = (int)($_POST['id_cita'] ?? 0);
            $dui_paciente  = trim($_POST['dui'] ?? '');
            $id_odontologo = (int)($_POST['id_odontologo'] ?? 0);
            $fecha_cita    = trim($_POST['fecha_cita'] ?? '');
            $hora_cita     = trim($_POST['hora_cita'] ?? '');
            $motivo        = trim($_POST['motivo'] ?? '');
            $estado        = trim($_POST['estado'] ?? 'programada');

            if (preg_match('/^\d{2}:\d{2}$/', $hora_cita)) {
                $hora_cita .= ':00';
            }

            $estadosValidos = ['programada', 'confirmada', 'atendida', 'cancelada'];
            if ($estado === '' || !in_array($estado, $estadosValidos, true)) {
                $estado = 'programada';
            }

            if (!$id_cita || !$dui_paciente || !$id_odontologo || !$fecha_cita || !$hora_cita) {
                $response = ['status' => 'error', 'message' => 'Datos incompletos para actualizar la cita'];
                break;
            }

            $ok = $citaModel->actualizarPorDui(
                $id_cita,
                $dui_paciente,
                $id_odontologo,
                $fecha_cita,
                $hora_cita,
                $motivo !== '' ? $motivo : null,
                $estado
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita actualizada exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar la cita. Verifique el DUI y la disponibilidad.'];
            break;
        case 'eliminar':
            $id = (int)($_POST['id'] ?? 0);

            if (!$id) {
                $response = ['status' => 'error', 'message' => 'ID de cita inválido'];
                break;
            }

            $ok = $citaModel->eliminar($id);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Cita eliminada']
                : ['status' => 'error', 'message' => 'No se pudo eliminar la cita'];
            break;

        default:
            break;
    }
} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
