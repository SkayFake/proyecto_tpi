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

        
        case 'listar':
            $rows = $pacienteModel->getPacientes();
            $response = ['status' => 'success', 'data' => $rows];
            break;

        case 'agregar':

            $nombre          = trim($_POST['nombre'] ?? '');
            $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
            $sexo            = trim($_POST['sexo'] ?? '');
            $telefono        = trim($_POST['telefono'] ?? '');
            $correo          = trim($_POST['correo'] ?? '');
            $direccion       = trim($_POST['direccion'] ?? '');
            $dui             = trim($_POST['dui'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            
            if (!$nombre || !$fechaNacimiento || !$sexo || !$telefono || !$correo) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Nombre, fecha de nacimiento, sexo, teléfono y correo son obligatorios'
                ];
                break;
            }

            // Validar formato correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Formato de correo inválido.'
                ];
                break;
            }

            // Teléfono
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
            $fechaFormateada = $fechaNacimiento;

            $ok = $pacienteModel->agregar(
                $nombre,
                $fechaFormateada,
                $sexo,
                $telefono,
                $correo,       
                $direccion,
                $duiParam,
                $notas
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente creado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo crear el paciente'];

            break;

        
        case 'obtener':
            $id = (int)($_GET['id'] ?? 0);
            $row = $pacienteModel->getPacienteById($id);

            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Paciente no encontrado'];
            break;

        
        case 'actualizar':

            $id              = (int)($_POST['id_paciente'] ?? 0);
            $nombre          = trim($_POST['nombre'] ?? '');
            $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
            $sexo            = trim($_POST['sexo'] ?? '');
            $telefono        = trim($_POST['telefono'] ?? '');
            $correo          = trim($_POST['correo'] ?? '');
            $direccion       = trim($_POST['direccion'] ?? '');
            $dui             = trim($_POST['dui'] ?? '');
            $notas           = trim($_POST['notas'] ?? '');

            if (!$id || !$nombre || !$fechaNacimiento || !$sexo || !$telefono || !$correo) {
                $response = ['status' => 'error', 'message' => 'Datos incompletos'];
                break;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $response = ['status' => 'error', 'message' => 'Formato de correo inválido.'];
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

            $fechaObj = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
            if (!$fechaObj) {
                $response = ['status' => 'error', 'message' => 'Fecha de nacimiento inválida'];
                break;
            }

            $fechaFormateada = $fechaObj->format('Y-m-d');

            $ok = $pacienteModel->actualizar(
                $id,
                $nombre,
                $fechaFormateada,
                $sexo,
                $telefono,
                $correo,      
                $direccion,
                $duiParam,
                $notas
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente actualizado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el paciente'];
            break;

        
        case 'eliminar':
            $id = (int)($_POST['id'] ?? 0);
            $ok = $pacienteModel->eliminar($id);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Paciente eliminado']
                : ['status' => 'error', 'message' => 'No se pudo eliminar el paciente'];
            break;

        default:
            break;
    }

} catch (PDOException $e) {

    $msg    = $e->getMessage();
    $codigo = $e->getCode();
    $userMsg = $msg;

    if ($codigo === '23000') {

        if (stripos($msg, 'uq_paciente_dui') !== false) {
            $userMsg = 'El DUI ya está registrado para otro paciente.';
        }
        elseif (stripos($msg, 'uq_paciente_telefono') !== false) {
            $userMsg = 'El teléfono ya está registrado para otro paciente.';
        }
        elseif (stripos($msg, 'uq_paciente_correo') !== false) {
            $userMsg = 'El correo ya está registrado para otro paciente.';
        }
        else {
            $userMsg = 'Restricción UNIQUE violada en la base de datos.';
        }
    }

    
    elseif ($codigo === '45000') {
        $userMsg = $msg;
    }

    $response = ['status' => 'error', 'message' => $userMsg];

} catch (Throwable $e) {
    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
