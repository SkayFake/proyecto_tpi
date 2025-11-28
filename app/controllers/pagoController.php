<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/pagoModel.php';
require_once __DIR__ . '/../models/pacienteModel.php';
require_once __DIR__ . '/../models/tratamientoModel.php';

$pagoModel       = new Pago();
$pacienteModel   = new Paciente();
$tratamientoModel = new Tratamiento();

$opcion = $_GET['opcion'] ?? null;

function errorJson(string $msg)
{
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

/**
 * Validaciones básicas para alta / edición de pago
 */
function validarDatosPagoBase(
    $id_paciente,
    $id_tratamiento,
    $metodo_pago,
    $monto,
    $estado_pago
) {
    if (!$id_paciente) {
        return "Debe seleccionar un paciente.";
    }

    if (!$id_tratamiento) {
        return "Debe seleccionar un tratamiento.";
    }

    if (!$metodo_pago) {
        return "Debe seleccionar un método de pago.";
    }

    if (!$monto || $monto <= 0) {
        return "El monto debe ser mayor que cero.";
    }

    if ($estado_pago === '' || $estado_pago === null) {
        return "Debe seleccionar el estado del pago.";
    }

    return true;
}

try {
    switch ($opcion) {

        /* ================== LISTAR PAGOS ================== */
        case 'listar':
            $rows = $pagoModel->getPagos();
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

        /* ================== LISTAR PACIENTES (SELECT) ================== */
        case 'listar_pacientes':
            echo json_encode([
                'status' => 'success',
                'data'   => $pacienteModel->getPacientes()
            ]);
            exit;

        /* ================== LISTAR TRATAMIENTOS (SELECT) ================== */
        case 'listar_tratamientos':
            echo json_encode([
                'status' => 'success',
                'data'   => $tratamientoModel->getTratamientosSelect()
            ]);
            exit;

        /* ================== BUSCAR PACIENTE POR CORREO ================== */
        case 'buscar_paciente_correo':
            $correo = trim($_GET['correo'] ?? '');
            if (!$correo) errorJson("Correo vacío.");

            $paciente = $pagoModel->buscarPacientePorCorreo($correo);
            if (!$paciente) errorJson("Paciente no encontrado con ese correo.");

            echo json_encode([
                'status' => 'success',
                'data'   => $paciente
            ]);
            exit;

        /* ================== OBTENER UN PAGO ================== */
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $row = $pagoModel->getPagoById($id);
            if (!$row) errorJson("Pago no encontrado.");

            echo json_encode(['status' => 'success', 'data' => $row]);
            exit;

        /* ================== AGREGAR PAGO ================== */
        case 'agregar':
            $id_paciente    = intval($_POST['id_paciente'] ?? 0);
            $id_tratamiento = intval($_POST['id_tratamiento'] ?? 0);
            $metodo_pago    = trim($_POST['metodo_pago'] ?? '');
            $monto          = floatval($_POST['monto'] ?? 0);
            $estado_pago    = trim($_POST['estado_pago'] ?? '');
            $referencia     = trim($_POST['referencia'] ?? '');

            // Validar los datos recibidos
            $valid = validarDatosPagoBase($id_paciente, $id_tratamiento, $metodo_pago, $monto, $estado_pago);
            if ($valid !== true) errorJson($valid);

            $ok = $pagoModel->agregar(
                $id_paciente,
                $id_tratamiento,
                $metodo_pago,
                $monto,
                $estado_pago,
                $referencia !== '' ? $referencia : null
            );

            if (!$ok) errorJson("No se pudo crear el pago.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Pago creado exitosamente'
            ]);
            exit;

        /* ================== ACTUALIZAR PAGO ================== */
        case 'actualizar':
            $id_pago        = intval($_POST['id_pago'] ?? 0);
            $id_paciente    = intval($_POST['id_paciente'] ?? 0);
            $id_tratamiento = intval($_POST['id_tratamiento'] ?? 0);
            $metodo_pago    = trim($_POST['metodo_pago'] ?? '');
            $monto          = floatval($_POST['monto'] ?? 0);
            $estado_pago    = trim($_POST['estado_pago'] ?? '');
            $referencia     = trim($_POST['referencia'] ?? '');

            if (!$id_pago) errorJson("ID inválido.");

            $pago = $pagoModel->getPagoById($id_pago);
            if (!$pago) errorJson("Pago no encontrado.");

            // Validar los datos recibidos
            $valid = validarDatosPagoBase($id_paciente, $id_tratamiento, $metodo_pago, $monto, $estado_pago);
            if ($valid !== true) errorJson($valid);

            $ok = $pagoModel->actualizar(
                $id_pago,
                $id_paciente,
                $id_tratamiento,
                $metodo_pago,
                $monto,
                $estado_pago,
                $referencia !== '' ? $referencia : null
            );

            if (!$ok) errorJson("No se pudo actualizar el pago.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Pago actualizado correctamente'
            ]);
            exit;

        /* ================== ELIMINAR PAGO ================== */
        case 'eliminar':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $ok = $pagoModel->eliminar($id);
            if (!$ok) errorJson("No se pudo eliminar el pago.");

            echo json_encode([
                'status'  => 'success',
                'message' => 'Pago eliminado'
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