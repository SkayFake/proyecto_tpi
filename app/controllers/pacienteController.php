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
                $direccion       = trim($_POST['direccion'] ?? '');
                $dui             = trim($_POST['dui'] ?? '');
                $notas           = trim($_POST['notas'] ?? '');
            
                if (!$nombre || !$fechaNacimiento || !$sexo || !$telefono || !$direccion || !$dui) {
                    $response = ['status'=>'error','message'=>'Todos los campos son obligatorios'];
                    break;
                }
                $fechaFormateada = $fechaNacimiento;
            
                $ok = $pacienteModel->agregar($nombre, $fechaFormateada, $sexo, $telefono, $direccion, $dui, $notas);
                $response = $ok
                    ? ['status'=>'success','message'=>'Paciente creado exitosamente']
                    : ['status'=>'error','message'=>'No se pudo crear el paciente'];
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
                $direccion       = trim($_POST['direccion'] ?? '');
                $dui             = trim($_POST['dui'] ?? '');
                $notas           = trim($_POST['notas'] ?? '');
            
                if (!$id || !$nombre || !$fechaNacimiento || !$sexo) {
                    $response = ['status' => 'error', 'message' => 'Datos incompletos'];
                    break;
                }
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
                    $dui,
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
} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);

?>
