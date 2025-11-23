<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/pacienteModel.php';

$pacienteModel = new Paciente();
$opcion = isset($_GET['opcion']) ? trim($_GET['opcion']) : null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

try {
    switch ($opcion) {

        /* ===================== LISTAR ===================== */
        case 'listar':
            $rows = $pacienteModel->getPacientes();
            $response = ['status' => 'success', 'data' => $rows];
            break;

        /* ===================== AGREGAR ===================== */
        case 'agregar':
            $nombre          = trim($_POST['nombre'] ?? '');
            $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? ''); // YYYY-MM-DD
            $sexo            = trim($_POST['sexo'] ?? '');
            $telefono        = trim($_POST['telefono'] ?? '');
            $direccion       = trim($_POST['direccion'] ?? '');
            $dui             = trim($_POST['dui'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            // Validaciones básicas: DUI no se exige aquí (lo decide la BD según la edad)
            if (!$nombre || !$fechaNacimiento || !$sexo || !$telefono) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Nombre, fecha de nacimiento, sexo y teléfono son obligatorios'
                ];
                break;
            }

            if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', $telefono)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Formato de teléfono inválido. Use 0000-0000.'
                ];
                break;
            }

            if ($dui !== '' && !preg_match('/^[0-9]{8}-[0-9]{1}$/', $dui)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Formato de DUI inválido. Use 00000000-0.'
                ];
                break;
            }

            $duiParam = ($dui === '') ? null : $dui;
            $fechaFormateada = $fechaNacimiento; // ya viene en Y-m-d

            $ok = $pacienteModel->agregar(
                $nombre,
                $fechaFormateada,
                $sexo,
                $telefono,
                $direccion,
                $duiParam,
                $notas
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente creado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo crear el paciente'];
            break;

        /* ===================== OBTENER ===================== */
        case 'obtener':
            $id = (int)($_GET['id'] ?? 0);
            $row = $pacienteModel->getPacienteById($id);
            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Paciente no encontrado'];
            break;

        /* ===================== ACTUALIZAR ===================== */
        case 'actualizar':
            $id              = (int)($_POST['id_paciente'] ?? 0);
            $nombre          = trim($_POST['nombre'] ?? '');
            $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
            $sexo            = trim($_POST['sexo'] ?? '');
            $telefono        = trim($_POST['telefono'] ?? '');
            $direccion       = trim($_POST['direccion'] ?? '');
            $dui             = trim($_POST['dui'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            if (!$id || !$nombre || !$fechaNacimiento || !$sexo || !$telefono) {
                $response = ['status' => 'error', 'message' => 'Datos incompletos'];
                break;
            }

            if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', $telefono)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Formato de teléfono inválido. Use 0000-0000.'
                ];
                break;
            }

            if ($dui !== '' && !preg_match('/^[0-9]{8}-[0-9]{1}$/', $dui)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Formato de DUI inválido. Use 00000000-0.'
                ];
                break;
            }

            $duiParam = ($dui === '') ? null : $dui;

            $dateTime = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
            if ($dateTime === false) {
                $response = ['status' => 'error', 'message' => 'Fecha de nacimiento inválida'];
                break;
            }
            $fechaFormateada = $dateTime->format('Y-m-d');

            $ok = $pacienteModel->actualizar(
                $id,
                $nombre,
                $fechaFormateada,
                $sexo,
                $telefono,
                $direccion,
                $duiParam,
                $notas
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente actualizado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el paciente'];
            break;

        /* ===================== ELIMINAR ===================== */
        case 'eliminar':
            $id = (int)($_POST['id'] ?? 0);
            $ok = $pacienteModel->eliminar($id);
            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente eliminado']
                : ['status' => 'error', 'message' => 'No se pudo eliminar el paciente'];
            break;

        default:
            // Opción inválida por defecto
            break;
    }

} catch (PDOException $e) {
    $msg    = $e->getMessage();
    $codigo = $e->getCode();
    $userMsg = 'Error al procesar la operación.';

    // Violación de UNIQUE u otra restricción
    if ($codigo === '23000') {
        if (stripos($msg, 'uq_paciente_dui') !== false) {
            $userMsg = 'El DUI ya está registrado para otro paciente.';
        } elseif (stripos($msg, 'uq_paciente_telefono') !== false) {
            $userMsg = 'El teléfono ya está registrado para otro paciente.';
        } else {
            $userMsg = 'Datos duplicados o restricción violada en la base de datos.';
        }
    }
    // Mensajes de SIGNAL en triggers (SQLSTATE '45000')
    elseif ($codigo === '45000') {
        $userMsg = $msg;
    } else {
        // En desarrollo puedes mostrar $msg; en producción algo más genérico
        $userMsg = $msg;
    }

    $response = ['status' => 'error', 'message' => $userMsg];

} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);

?>
