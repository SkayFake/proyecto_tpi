<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/pagoModel.php';
require_once __DIR__ . '/../models/pacienteModel.php';
require_once __DIR__ . '/../models/tratamientoModel.php';

$pagoModel        = new Pago();
$pacienteModel    = new Paciente();
$tratamientoModel = new Tratamiento();

$opcion = $_GET['opcion'] ?? null;

function errorJson(string $msg)
{
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

function validarDatosPagoBase($id_paciente, $id_tratamiento, $fecha_pago, $metodo_pago, $monto, $estado_pago)
{
    if (!$id_paciente || $id_paciente <= 0) {
        return "Debe seleccionar un paciente válido.";
    }

    if (!$id_tratamiento || $id_tratamiento <= 0) {
        return "Debe seleccionar un tratamiento válido.";
    }

    if (!$fecha_pago) {
        return "Debe seleccionar una fecha de pago.";
    }

    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha_pago);
    if (!$fechaObj) {
        return "Formato de fecha inválido.";
    }

    $hoy = new DateTime('today');
    if ($fechaObj > $hoy) {
        return "La fecha de pago no puede ser futura.";
    }

    if (!$metodo_pago || trim($metodo_pago) === '') {
        return "Debe seleccionar un método de pago.";
    }

    $metodos_validos = ['efectivo', 'tarjeta', 'transferencia', 'cheque'];
    if (!in_array(strtolower($metodo_pago), $metodos_validos)) {
        return "El método de pago no es válido.";
    }

    if (!is_numeric($monto) || $monto <= 0) {
        return "El monto debe ser un número positivo.";
    }

    if (!$estado_pago || trim($estado_pago) === '') {
        return "Debe seleccionar el estado del pago.";
    }

    $estados_validos = ['pendiente', 'completado', 'rechazado', 'reembolsado'];
    if (!in_array(strtolower($estado_pago), $estados_validos)) {
        return "El estado del pago no es válido.";
    }

    return true;
}

try {

    switch ($opcion) {

        // =====================
        // LISTAR TODOS LOS PAGOS
        // =====================
        case 'listar':
            $rows = $pagoModel->getPagos();
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

        // =====================
        // LISTAR PAGOS POR PACIENTE
        // =====================
        case 'listar_por_paciente':
            $id_paciente = intval($_GET['id_paciente'] ?? 0);
            if (!$id_paciente) errorJson("ID de paciente inválido.");

            $rows = $pagoModel->getPagosByPaciente($id_paciente);
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

        // =====================
        // LISTAR PAGOS POR TRATAMIENTO
        // =====================
        case 'listar_por_tratamiento':
            $id_tratamiento = intval($_GET['id_tratamiento'] ?? 0);
            if (!$id_tratamiento) errorJson("ID de tratamiento inválido.");

            $rows = $pagoModel->getPagosByTratamiento($id_tratamiento);
            echo json_encode(['status' => 'success', 'data' => $rows]);
            exit;

        // =====================
        // OBTENER TOTAL PAGADO DE UN TRATAMIENTO
        // =====================
        case 'total_pagado_tratamiento':
            $id_tratamiento = intval($_GET['id_tratamiento'] ?? 0);
            if (!$id_tratamiento) errorJson("ID de tratamiento inválido.");

            $total = $pagoModel->getTotalPagadoTratamiento($id_tratamiento);
            echo json_encode(['status' => 'success', 'total' => $total]);
            exit;

        // =====================
        // LISTAR PACIENTES (para select)
        // =====================
        case 'listar_pacientes':
            // Asumiendo que tienes un método similar en PacienteModel
            $pacientes = $pacienteModel->getPacientes();
            echo json_encode(['status' => 'success', 'data' => $pacientes]);
            exit;

        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $row = $pagoModel->getPagoById($id);
            if (!$row) errorJson("Pago no encontrado.");

            echo json_encode(['status' => 'success', 'data' => $row]);
            exit;

        // =====================
        // AGREGAR NUEVO PAGO
        // =====================
        case 'agregar':
            $id_paciente    = intval($_POST['id_paciente'] ?? 0);
            $id_tratamiento = intval($_POST['id_tratamiento'] ?? 0);
            $fecha_pago     = trim($_POST['fecha_pago'] ?? '');
            $metodo_pago    = trim($_POST['metodo_pago'] ?? '');
            $monto          = floatval($_POST['monto'] ?? 0);
            $estado_pago    = trim($_POST['estado_pago'] ?? '');
            $referencia     = trim($_POST['referencia'] ?? '');

            $valid = validarDatosPagoBase(
                $id_paciente,
                $id_tratamiento,
                $fecha_pago,
                $metodo_pago,
                $monto,
                $estado_pago
            );

            if ($valid !== true) errorJson($valid);

            // Validar que el paciente existe
            $paciente = $pacienteModel->getPacienteById($id_paciente);
            if (!$paciente) errorJson("El paciente no existe.");

            // Validar que el tratamiento existe
            $tratamiento = $tratamientoModel->getTratamientoById($id_tratamiento);
            if (!$tratamiento) errorJson("El tratamiento no existe.");

            // Validar que el tratamiento pertenece al paciente
            if ($tratamiento['id_paciente'] != $id_paciente) {
                errorJson("El tratamiento no pertenece al paciente seleccionado.");
            }

            // Validar referencia única si se proporciona
            if ($referencia !== '' && $pagoModel->existeReferencia($referencia)) {
                errorJson("Ya existe un pago con esa referencia.");
            }

            // Validar que el monto no exceda el saldo pendiente
            $total_pagado = $pagoModel->getTotalPagadoTratamiento($id_tratamiento);
            $subtotal_tratamiento = floatval($tratamiento['subtotal']);
            $saldo_pendiente = $subtotal_tratamiento - $total_pagado;

            if ($monto > $saldo_pendiente) {
                errorJson("El monto excede el saldo pendiente del tratamiento ($" . number_format($saldo_pendiente, 2) . ").");
            }

            $ok = $pagoModel->agregar(
                $id_paciente,
                $id_tratamiento,
                $fecha_pago,
                $metodo_pago,
                $monto,
                $estado_pago,
                $referencia ?: null
            );

            if (!$ok) errorJson("No se pudo registrar el pago.");

            echo json_encode(['status' => 'success', 'message' => 'Pago registrado exitosamente']);
            exit;

        // =====================
        // ACTUALIZAR PAGO
        // =====================
        case 'actualizar':
            $id_pago        = intval($_POST['id_pago'] ?? 0);
            $id_paciente    = intval($_POST['id_paciente'] ?? 0);
            $id_tratamiento = intval($_POST['id_tratamiento'] ?? 0);
            $fecha_pago     = trim($_POST['fecha_pago'] ?? '');
            $metodo_pago    = trim($_POST['metodo_pago'] ?? '');
            $monto          = floatval($_POST['monto'] ?? 0);
            $estado_pago    = trim($_POST['estado_pago'] ?? '');
            $referencia     = trim($_POST['referencia'] ?? '');

            if (!$id_pago) errorJson("ID inválido.");

            $pago = $pagoModel->getPagoById($id_pago);
            if (!$pago) errorJson("Pago no encontrado.");

            // Validar que no se edite un pago completado hace más de 24 horas
            if ($pago['estado_pago'] === 'completado') {
                $fecha_pago_original = new DateTime($pago['fecha_pago']);
                $ahora = new DateTime();
                $diferencia = $ahora->diff($fecha_pago_original);
                
                if ($diferencia->days > 1) {
                    errorJson("No se puede editar un pago completado hace más de 24 horas.");
                }
            }

            $valid = validarDatosPagoBase(
                $id_paciente,
                $id_tratamiento,
                $fecha_pago,
                $metodo_pago,
                $monto,
                $estado_pago
            );

            if ($valid !== true) errorJson($valid);

            // Validar que el paciente existe
            $paciente = $pacienteModel->getPacienteById($id_paciente);
            if (!$paciente) errorJson("El paciente no existe.");

            // Validar que el tratamiento existe
            $tratamiento = $tratamientoModel->getTratamientoById($id_tratamiento);
            if (!$tratamiento) errorJson("El tratamiento no existe.");

            // Validar que el tratamiento pertenece al paciente
            if ($tratamiento['id_paciente'] != $id_paciente) {
                errorJson("El tratamiento no pertenece al paciente seleccionado.");
            }

            // Validar referencia única (excluyendo el pago actual)
            if ($referencia !== '' && $referencia !== $pago['referencia']) {
                if ($pagoModel->existeReferencia($referencia)) {
                    errorJson("Ya existe otro pago con esa referencia.");
                }
            }

            // Validar saldo pendiente
            $total_pagado = $pagoModel->getTotalPagadoTratamiento($id_tratamiento);
            $monto_original = floatval($pago['monto']);
            $total_sin_este_pago = $total_pagado - $monto_original;
            $subtotal_tratamiento = floatval($tratamiento['subtotal']);
            $saldo_disponible = $subtotal_tratamiento - $total_sin_este_pago;

            if ($monto > $saldo_disponible) {
                errorJson("El monto excede el saldo disponible del tratamiento ($" . number_format($saldo_disponible, 2) . ").");
            }

            $ok = $pagoModel->actualizar(
                $id_pago,
                $id_paciente,
                $id_tratamiento,
                $fecha_pago,
                $metodo_pago,
                $monto,
                $estado_pago,
                $referencia ?: null
            );

            if (!$ok) errorJson("No se pudo actualizar el pago.");

            echo json_encode(['status' => 'success', 'message' => 'Pago actualizado correctamente']);
            exit;

        // =====================
        // ELIMINAR PAGO
        // =====================
        case 'eliminar':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $pago = $pagoModel->getPagoById($id);
            if (!$pago) errorJson("Pago no encontrado.");

            // Validar que no se elimine un pago completado
            if ($pago['estado_pago'] === 'completado') {
                errorJson("No se puede eliminar un pago completado. Considere cambiarlo a 'reembolsado'.");
            }

            $ok = $pagoModel->eliminar($id);
            if (!$ok) errorJson("No se pudo eliminar el pago.");

            echo json_encode(['status' => 'success', 'message' => 'Pago eliminado']);
            exit;

        // =====================
        // VALIDAR SALDO DISPONIBLE
        // =====================
        case 'validar_saldo':
            $id_tratamiento = intval($_GET['id_tratamiento'] ?? 0);
            $monto          = floatval($_GET['monto'] ?? 0);

            if (!$id_tratamiento) errorJson("ID de tratamiento inválido.");

            $tratamiento = $tratamientoModel->getTratamientoById($id_tratamiento);
            if (!$tratamiento) errorJson("Tratamiento no encontrado.");

            $total_pagado = $pagoModel->getTotalPagadoTratamiento($id_tratamiento);
            $subtotal = floatval($tratamiento['subtotal']);
            $saldo_pendiente = $subtotal - $total_pagado;

            if ($monto > $saldo_pendiente) {
                errorJson("El monto ($" . number_format($monto, 2) . ") excede el saldo pendiente ($" . number_format($saldo_pendiente, 2) . ").");
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Monto válido',
                'saldo_pendiente' => $saldo_pendiente,
                'total_tratamiento' => $subtotal,
                'total_pagado' => $total_pagado
            ]);
            exit;

        // =====================
        // OBTENER DETALLE FINANCIERO DE TRATAMIENTO
        // =====================
        case 'detalle_financiero':
            $id_tratamiento = intval($_GET['id_tratamiento'] ?? 0);
            if (!$id_tratamiento) errorJson("ID de tratamiento inválido.");

            $tratamiento = $tratamientoModel->getTratamientoById($id_tratamiento);
            if (!$tratamiento) errorJson("Tratamiento no encontrado.");

            $total_pagado = $pagoModel->getTotalPagadoTratamiento($id_tratamiento);
            $subtotal = floatval($tratamiento['subtotal']);
            $saldo_pendiente = $subtotal - $total_pagado;

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'subtotal_tratamiento' => $subtotal,
                    'total_pagado' => $total_pagado,
                    'saldo_pendiente' => $saldo_pendiente,
                    'porcentaje_pagado' => $subtotal > 0 ? round(($total_pagado / $subtotal) * 100, 2) : 0
                ]
            ]);
            exit;

        default:
            errorJson("Opción no válida.");
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}