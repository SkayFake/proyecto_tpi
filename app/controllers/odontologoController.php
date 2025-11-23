<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/odontologoModel.php';

$odontologoModel = new Odontologo();
$opcion = isset($_GET['opcion']) ? trim($_GET['opcion']) : null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

try {
    switch ($opcion) {
        case 'listar':
            $rows = $odontologoModel->getOdontologos();
            $response = ['status' => 'success', 'data' => $rows];
            break;

        case 'agregar':
            $nombre       = trim($_POST['nombre'] ?? '');
            $correo       = trim($_POST['correo'] ?? '');
            $contrasenia  = trim($_POST['contrasenia'] ?? '');
            $telefono     = trim($_POST['telefono'] ?? '');
            $especialidad = trim($_POST['especialidad'] ?? '');
            $es_admin     = isset($_POST['es_admin']) ? (int)$_POST['es_admin'] : 0;
            $estado       = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

            if (!$nombre || !$correo || !$contrasenia) {
                $response = ['status' => 'error', 'message' => 'Nombre, correo y contraseña son obligatorios'];
                break;
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $response = ['status' => 'error', 'message' => 'Formato de correo inválido'];
                break;
            }
            if ($odontologoModel->existeCorreo($correo)) {
                $response = ['status' => 'error', 'message' => 'El correo ya está registrado'];
                break;
            }

            $ok = $odontologoModel->agregar(
                $nombre,
                $correo,
                $contrasenia,
                $telefono ?: null,
                $especialidad ?: null,
                $es_admin,
                $estado
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Odontólogo creado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo crear el odontólogo'];
            break;


        case 'obtener':
            $id = (int)($_GET['id'] ?? 0);
            $row = $odontologoModel->getOdontologoById($id);
            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Odontólogo no encontrado'];
            break;

        case 'actualizar':
            $id           = (int)($_POST['id_odontologo'] ?? 0);
            $nombre       = trim($_POST['nombre'] ?? '');
            $correo       = trim($_POST['correo'] ?? '');
            $telefono     = trim($_POST['telefono'] ?? '');
            $especialidad = trim($_POST['especialidad'] ?? '');
            $es_admin     = isset($_POST['es_admin']) ? (int)$_POST['es_admin'] : null;
            $estado       = isset($_POST['estado']) ? (int)$_POST['estado'] : null;
            $contrasenia  = isset($_POST['contrasenia']) ? trim($_POST['contrasenia']) : null;

            if ($contrasenia === '') {
                $contrasenia = null; 
            }

            if (!$id || !$nombre || !$correo) {
                $response = ['status' => 'error', 'message' => 'Datos incompletos'];
                break;
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $response = ['status' => 'error', 'message' => 'Formato de correo inválido'];
                break;
            }

            $ok = $odontologoModel->actualizar(
                $id,
                $nombre,
                $correo,
                $telefono ?: null,
                $especialidad ?: null,
                $es_admin,
                $estado,
                $contrasenia 
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Odontólogo actualizado exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el odontólogo'];
            break;


        case 'actualizar_contrasenia':
            $id = (int)($_POST['id_odontologo'] ?? 0);
            $nueva_contrasenia = trim($_POST['nueva_contrasenia'] ?? '');

            if (!$id || !$nueva_contrasenia) {
                $response = ['status' => 'error', 'message' => 'ID y nueva contraseña son obligatorios'];
                break;
            }

            if (strlen($nueva_contrasenia) < 6) {
                $response = ['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres'];
                break;
            }

            $ok = $odontologoModel->actualizarContrasenia($id, $nueva_contrasenia);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Contraseña actualizada exitosamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar la contraseña'];
            break;

        case 'eliminar':
            $id = (int)($_POST['id'] ?? 0);
            $ok = $odontologoModel->eliminar($id);
            $response = $ok
                ? ['status' => 'success', 'message' => 'Odontólogo eliminado']
                : ['status' => 'error', 'message' => 'No se pudo eliminar el odontólogo'];
            break;

        case 'estado':
            $id     = (int)($_POST['id'] ?? 0);
            $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : null;

            if (!$id || !in_array($estado, [0, 1], true)) {
                $response = ['status' => 'error', 'message' => 'Datos de estado inválidos'];
                break;
            }

            $ok = $odontologoModel->actualizarEstado($id, $estado);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Estado actualizado correctamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el estado'];
            break;


        default:
            break;
    }
} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
