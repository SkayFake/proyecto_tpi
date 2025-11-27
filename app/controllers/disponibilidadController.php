<?php
header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/disponibilidadModel.php';
require_once __DIR__ . '/../models/odontologoModel.php';

$disModel = new Disponibilidad();
$odontologoModel = new Odontologo();

$opcion = $_GET['opcion'] ?? null;

function errorJson(string $msg)
{
    echo json_encode(["status" => "error", "message" => $msg]);
    exit;
}


function validarDisponibilidad($id_odontologo, $fi, $ff, $hi, $hf, $cupo)
{
    if (!$id_odontologo || !$fi || !$ff || !$hi || !$hf || !$cupo)
        return "Debe completar todos los campos.";

    // Horas exactas XX:00
    if (substr($hi, 3, 2) !== "00" || substr($hf, 3, 2) !== "00")
        return "Las horas deben ser exactas. Ejemplo: 08:00, 10:00.";

    // Fechas válidas
    if ($fi > $ff)
        return "La fecha de inicio no puede ser mayor que la fecha fin.";

    // Horas válidas
    if ($hi >= $hf)
        return "La hora inicio debe ser menor a la hora fin.";

    // No permitir rangos de más de 12 horas
    if ((strtotime($hf) - strtotime($hi)) > (12 * 3600))
        return "No se pueden registrar más de 12 horas por día.";

    // No permitir disponibilidad totalmente en el pasado
    if ($ff < date("Y-m-d"))
        return "No puede registrar disponibilidad en fechas ya pasadas.";

    // No permitir domingos
    $inicio = new DateTime($fi);
    $fin = new DateTime($ff);

    while ($inicio <= $fin) {
        if ($inicio->format("w") == 0) {
            return "No se permite registrar disponibilidad incluyendo domingos.";
        }
        $inicio->modify("+1 day");
    }

    // Validación de cupo
    if ($cupo <= 0)
        return "El cupo debe ser mayor que 0.";

    return true;
}




try {

    switch ($opcion) {

        
        case "listar":
            $data = $disModel->listar();
            echo json_encode(["status" => "success", "data" => $data]);
            break;

        case "listar_odontologos":
            $rows = $odontologoModel->getOdontologosSelect();
            echo json_encode(["status" => "success", "data" => $rows]);
            break;

  
        case "obtener":
            $id = intval($_GET['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $row = $disModel->obtener($id);
            if (!$row) errorJson("No existe esta disponibilidad.");

            echo json_encode(["status" => "success", "data" => $row]);
            break;
case "agregar":

    $id_odontologo = intval($_POST['id_odontologo']);
    $fi = trim($_POST['fecha_inicio']);
    $ff = trim($_POST['fecha_fin']);
    $hi = trim($_POST['hora_inicio']);
    $hf = trim($_POST['hora_fin']);
    $notas = trim($_POST['notas'] ?? '');
    $cupo = intval($_POST['cupo']); 

    $valid = validarDisponibilidad($id_odontologo, $fi, $ff, $hi, $hf, $cupo);
    if ($valid !== true) errorJson($valid);

    if ($disModel->existeTraslapeCompleto($id_odontologo, $fi, $ff, $hi, $hf)) {
        errorJson("Esta disponibilidad se cruza con otra existente.");
    }

    $ok = $disModel->agregar($id_odontologo, $fi, $ff, $hi, $hf, $notas, $cupo); // Pasar el cupo al modelo

    echo json_encode([
        "status"  => $ok ? "success" : "error",
        "message" => $ok ? "Disponibilidad creada" : "No se pudo crear"
    ]);
    break;


       
        case "actualizar":

    $id = intval($_POST['id_disponibilidad']);
    if (!$id) errorJson("ID inválido.");

    $id_odontologo = intval($_POST['id_odontologo']);
    $fi = trim($_POST['fecha_inicio']);
    $ff = trim($_POST['fecha_fin']);
    $hi = trim($_POST['hora_inicio']);
    $hf = trim($_POST['hora_fin']);
    $notas = trim($_POST['notas'] ?? '');
    $cupo = intval($_POST['cupo']); 

    $valid = validarDisponibilidad($id_odontologo, $fi, $ff, $hi, $hf, $cupo);
    if ($valid !== true) errorJson($valid);

    if ($disModel->existeTraslapeCompleto($id_odontologo, $fi, $ff, $hi, $hf, $id)) {
        errorJson("El rango se traslapa con otra disponibilidad.");
    }

    $ok = $disModel->actualizar($id, $id_odontologo, $fi, $ff, $hi, $hf, $notas, $cupo); 

    echo json_encode([
        "status"  => $ok ? "success" : "error",
        "message" => $ok ? "Disponibilidad actualizada" : "No se pudo actualizar"
    ]);
    break;


       
        case "eliminar":
            $id = intval($_POST['id'] ?? 0);
            if (!$id) errorJson("ID inválido.");

            $ok = $disModel->eliminar($id);

            echo json_encode([
                "status"  => $ok ? "success" : "error",
                "message" => $ok ? "Disponibilidad eliminada" : "No se pudo eliminar"
            ]);
            break;

        default:
            errorJson("Opción no válida.");
    }

} catch (Throwable $e) {
    errorJson($e->getMessage());
}
