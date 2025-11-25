<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/odontogramaModel.php';

$odontogramaModel = new Odontograma();

$opcion = $_GET['opcion'] ?? null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

try {
    switch ($opcion) {

        case 'listar':
            $data = $odontogramaModel->listar();
            echo json_encode([
                'status' => 'success',
                'data'   => $data
            ]);
            exit;


        case 'buscar_paciente_nombre':
            $nombre = trim($_GET['nombre'] ?? '');

            if ($nombre === '') {
                $response = ['status' => 'error', 'message' => 'Debe ingresar un nombre'];
                break;
            }

            $paciente = $odontogramaModel->buscarPorNombrePaciente($nombre);

            if (!$paciente) {
                $response = [
                    'status' => 'error',
                    'message' => 'No se encontró un paciente con ese nombre'
                ];
            } else {
                $response = ['status' => 'success', 'data' => $paciente];
            }
            break;

        case 'obtener':
            $id = intval($_GET['id'] ?? 0);

            if (!$id) {

                $response = ['status' => 'error', 'message' => 'ID de odontograma inválido'];
                break;
            }
            $row = $odontogramaModel->obtenerPorId($id);

            $response = $row ?
                ['status' => 'success', 'data' => $row] :
                ['status' => 'error', 'message' => 'Odontograma no encontrado'];
            break;

        case 'foto':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
                return;
            }
            $bin = $odontogramaModel->obtenerFoto($id);
            if ($bin === "") {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Sin Foto']);
                return;
            }
            $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
            $mime  = ($finfo ? finfo_buffer($finfo, $bin) : null) ?: 'image/jpeg';
            if ($finfo) finfo_close($finfo);

            header_remove('Content-Type');
            header('Content-Type: ' . $mime);
            header('Cache-Control: private, max-age=604800');
            echo $bin;
            return;
            break;
        case 'agregar':
            $nombre_paciente = trim($_POST['name_paciente'] ?? '');
            $imagen = null;
            if (!empty($_FILES['imagen']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
                if ((int)$_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                    $response = ['status' => 'error', 'message' => 'Error al subir la imagen'];
                    break;
                }
                $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
            }
            $observaciones = trim($_POST['observaciones'] ?? '');

            $paciente = $odontogramaModel->buscarPorNombrePaciente($nombre_paciente);
            if (!$paciente) {
                $response = [
                    'status' => 'error',
                    'message' => 'No se encontró un paciente con ese nombre'
                ];
                break;
            }

            $id_paciente = $paciente['id_paciente'];

            $ok = $odontogramaModel->agregar($id_paciente, $imagen, $observaciones);
            $response = $ok
                ? ['status' => 'success', 'message' => 'Odontograma agregado']
                : ['status' => 'error', 'message' => 'No se pudo agregar el odontograma'];
            break;

        case 'actualizar':
    $id_odontograma = intval($_POST['id_odontograma'] ?? 0);
    $nombre_paciente = trim($_POST['name_paciente'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');
    $borrar_foto = isset($_POST['borrar_foto']) && $_POST['borrar_foto'] === '1';
    
    $imagen = null;
    

    if ($borrar_foto) {
        $imagen = ''; 
    }
   
    else if (!empty($_FILES['imagen']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
        if ((int)$_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $response = ['status' => 'error', 'message' => 'Error al subir la imagen'];
            break;
        }
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    if (!$id_odontograma) {
        $response = ['status' => 'error', 'message' => 'ID de odontograma inválido'];
        break;
    }

    $paciente = $odontogramaModel->buscarPorNombrePaciente($nombre_paciente);
    if (!$paciente) {
        $response = [
            'status' => 'error',
            'message' => 'No se encontró un paciente con ese nombre'
        ];
        break;
    }

    $id_paciente = $paciente['id_paciente'];

    $ok = $odontogramaModel->actualizar($id_odontograma, $id_paciente, $imagen, $observaciones);
    $response = $ok
        ? ['status' => 'success', 'message' => 'Odontograma actualizado']
        : ['status' => 'error', 'message' => 'No se pudo actualizar el odontograma'];
    break;
        case 'eliminar':
            $id_odontograma = intval($_POST['id_odontograma'] ?? 0);
            if (!$id_odontograma) {
                $response = ['status' => 'error', 'message' => 'ID de odontograma inválido'];
                break;
            }
            $ok = $odontogramaModel->eliminar($id_odontograma);
            $response = $ok
                ? ['status' => 'success', 'message' => 'Odontograma eliminado']
                : ['status' => 'error', 'message' => 'No se pudo eliminar el odontograma'];
            break;
        default:
            $response = ['status' => 'error', 'message' => 'Opción inválida'];
            break;
    }
} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}
echo json_encode($response);
