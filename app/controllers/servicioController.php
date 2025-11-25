<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/servicioModel.php';

$servicioModel = new Servicio();

$opcion = $_GET['opcion'] ?? null;

$response = ['status' => 'error', 'message' => 'Opción inválida'];

try {

    switch ($opcion) {

        case 'listar':
            $data = $servicioModel->getServicios();
            echo json_encode([
                'status' => 'success',
                'data'   => $data
            ]);
            exit;

        case 'obtener':
            $id = (int)($_GET['id'] ?? 0);

            if ($id <= 0) {
                $response = ['status' => 'error', 'message' => 'ID de servicio inválido'];
                break;
            }

            $row = $servicioModel->getServicioById($id);

            $response = $row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Servicio no encontrado'];
            break;


        case 'agregar':

            $codigo      = trim($_POST['codigo'] ?? '');
            $nombre      = trim($_POST['nombre_servicio'] ?? '');
            $precio      = isset($_POST['precio_base']) ? (float)$_POST['precio_base'] : 0.0;
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activo      = (int)($_POST['activo'] ?? 1);

            $duracion_dias = (int)($_POST['duracion_dias'] ?? 0);
            $fecha_venc    = trim($_POST['fecha_vencimiento'] ?? '');

            if ($nombre === '' || $precio <= 0) {
                $response = ['status' => 'error', 'message' => 'Nombre o precio inválidos'];
                break;
            }

            if ($duracion_dias > 0 && $fecha_venc === '') {
                $fecha_venc = date('Y-m-d H:i:s', strtotime("+{$duracion_dias} days"));
            }
            if ($fecha_venc === '') {
                $fecha_venc = null;
            }

            
            if ($servicioModel->existeNombreActivo($nombre)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Ya existe un servicio activo con ese nombre'
                ];
                break;
            }

            $ok = $servicioModel->agregar(
                $codigo,
                $nombre,
                (float)$precio,                         
                $descripcion !== '' ? $descripcion : null,
                $activo,
                $fecha_venc
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Servicio agregado correctamente']
                : ['status' => 'error', 'message' => 'No se pudo agregar el servicio'];
            break;


        case 'actualizar':

            $id_servicio = (int)($_POST['id_servicio'] ?? 0);
            if ($id_servicio <= 0) {
                $response = ['status' => 'error', 'message' => 'ID de servicio inválido'];
                break;
            }
            
            $codigo      = trim($_POST['codigo'] ?? '');
            $nombre      = trim($_POST['nombre_servicio'] ?? '');
            $precio      = isset($_POST['precio_base']) ? (float)$_POST['precio_base'] : 0.0;
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activo      = (int)($_POST['activo'] ?? 1);

            $duracion_dias = (int)($_POST['duracion_dias'] ?? 0);
            $fecha_venc    = trim($_POST['fecha_vencimiento'] ?? '');

            if ($nombre === '' || $precio <= 0) {
                $response = ['status' => 'error', 'message' => 'Datos inválidos'];
                break;
            }

            if ($duracion_dias > 0 && $fecha_venc === '') {
                $fecha_venc = date('Y-m-d H:i:s', strtotime("+{$duracion_dias} days"));
            }
            if ($fecha_venc === '') {
                $fecha_venc = null;
            }

            if ($activo === 1 && $servicioModel->existeNombreActivo($nombre, $id_servicio)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Ya existe otro servicio activo con ese nombre'
                ];
                break;
            }

            $modificado_por = null;

            $ok = $servicioModel->actualizar(
                $id_servicio,
                $codigo,
                $nombre,
                (float)$precio,                       
                $descripcion !== '' ? $descripcion : null,
                $activo,
                $fecha_venc,
                $modificado_por
            );

            $response = $ok
                ? ['status' => 'success', 'message' => 'Servicio actualizado correctamente']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el servicio'];

            break;

        case 'eliminar':
            $id_servicio = (int)($_POST['id_servicio'] ?? 0);

            if ($id_servicio <= 0) {
                $response = ['status' => 'error', 'message' => 'ID de servicio inválido'];
                break;
            }

            $ok = $servicioModel->eliminar($id_servicio);

            $response = $ok
                ? ['status' => 'success', 'message' => 'Servicio eliminado']
                : ['status' => 'error', 'message' => 'No se pudo eliminar el servicio'];
            break;


        default:
            $response = ['status' => 'error', 'message' => 'Opción inválida'];
            break;
    }

} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
