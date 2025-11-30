<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/expedienteModel.php';

$exp = new ExpedienteModel();
$action = $_GET['action'] ?? null;

function respond($arr) {
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === "buscar") {

    $raw = file_get_contents("php://input");
    error_log("RAW INPUT => " . $raw);

    $json = json_decode($raw, true);

    if ($json === null) {
        error_log("JSON ERROR => " . json_last_error_msg());
        respond([
            'success' => false,
            'message' => "Error al procesar JSON: " . json_last_error_msg(),
            'raw' => $raw
        ]);
    }

    $nombre = trim($json['nombre_paciente'] ?? '');

    if ($nombre === '') {
        respond(['success' => false, 'message' => 'Debe ingresar un nombre de paciente']);
    }

    $rows = $exp->obtenerExpedientePorNombre($nombre);

    error_log("ROWS COUNT => " . count($rows));
    error_log("ROWS DATA => " . print_r($rows, true));

    if (!$rows) {
        respond(['success' => false, 'message' => 'No se encontró expediente del paciente']);
    }

   //tengo datos del paciente
    $paciente = [
        'nombre' => $rows[0]['nombre_paciente1'] ?? '',
        'fecha_nacimiento' => $rows[0]['fecha_nacimiento'] ?? '',
        'sexo' => $rows[0]['sexo'] ?? '',
        'telefono' => $rows[0]['telefono'] ?? '',
        'correo' => $rows[0]['correo'] ?? '',
        'direccion' => $rows[0]['direccion'] ?? '',
        'dui' => $rows[0]['dui'] ?? '',
        'notas' => $rows[0]['paciente_notas'] ?? '',
    ];

   //datos del odontograma
    $odontogramas = [];

    foreach ($rows as $r) {
        if (!empty($r['odontograma_fecha'])) {

            $img = $r['odontograma_imagen'] ?: null;
            $odontogramas[] = [
                'odontograma_fecha' => $r['odontograma_fecha'],
                'odontograma_observaciones' => $r['odontograma_observaciones'],
                'odontograma_imagen' => $img
            ];
        }
    }

    //datos del tratamiento
    $tratamientos = [];

    foreach ($rows as $r) {
        if (!empty($r['tratamiento_nombre'])) {
            $tratamientos[] = [
                'tratamiento_nombre' => $r['tratamiento_nombre'],
                'tratamiento_estado' => $r['tratamiento_estado'],
                'tratamiento_notas' => $r['tratamiento_notas']
            ];
        }
    }

//lleno clases
    respond([
        'success' => true,
        'paciente' => $paciente,
        'odontogramas' => $odontogramas,
        'tratamientos' => $tratamientos
    ]);
}

if ($action === "exportar") {

    $nombre = $_GET['nombre'] ?? '';

    if (!$nombre) {
        die("Nombre inválido");
    }

    $rows = $exp->obtenerExpedientePorNombre($nombre);

    if (!$rows) {
        die("No se encontró expediente para exportar");
    }
    //guardo la imagen temporal
    $tmpFiles = [];

    function crearArchivoTemporalDesdeBlob(string $blob, array &$tmpFiles): ?string
    {
    // mira si las imagenes son diferente a png o jpg
        if (strpos($blob, "\x89PNG") === 0) {
            $ext = ".png";
        } else {
            $ext = ".jpg";
        }

        $tmp = __DIR__ . "/tmp_odontograma_" . uniqid() . $ext;
        file_put_contents($tmp, $blob);

        $tmpFiles[] = $tmp;
        return $tmp;
    }

    //borro archivos temporales
    function limpiarTemporales(array $tmpFiles)
    {
        foreach ($tmpFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
    header('Content-Type: application/pdf');

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    //head pdf
    $pdf->Cell(0, 10, 'Expediente Medico - ' . $nombre, 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $p = $rows[0]; //datos 

   //lleno pacinete
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Datos del Paciente:", 0, 1);
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(0, 8, "Fecha de nacimiento: " . $p['fecha_nacimiento']); $pdf->Ln(6);
    $pdf->Cell(0, 8, "Sexo: " . $p['sexo']); $pdf->Ln(6);
    $pdf->Cell(0, 8, "Telefono: " . $p['telefono']); $pdf->Ln(6);
    $pdf->Cell(0, 8, "Correo: " . $p['correo']); $pdf->Ln(6);
    $pdf->Cell(0, 8, "Direccion: " . $p['direccion']); $pdf->Ln(6);
    $pdf->Cell(0, 8, "DUI: " . $p['dui']); $pdf->Ln(10);

    //lleno odontograma
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Odontogramas:", 0, 1);
    $pdf->SetFont('Arial', '', 12);

    foreach ($rows as $r) {

        if (!empty($r['odontograma_fecha'])) {

            $pdf->Cell(0, 8, "Fecha: " . $r['odontograma_fecha']);
            $pdf->Ln(6);

            //ver si hay imagen
            if (!empty($r['odontograma_imagen_blob'])) {

                $archivoTemp = crearArchivoTemporalDesdeBlob($r['odontograma_imagen_blob'], $tmpFiles);

                if ($archivoTemp) {
                    $pdf->Image($archivoTemp, null, null, 80);
                    $pdf->Ln(8);
                }
            }

            $pdf->MultiCell(0, 8, "Observaciones: " . ($r['odontograma_observaciones'] ?? 'Sin observaciones'));
            $pdf->Ln(5);
        }
    }

    //lleno tratamiento
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Tratamientos:", 0, 1);
    $pdf->SetFont('Arial', '', 12);

    foreach ($rows as $r) {
        if (!empty($r['tratamiento_nombre'])) {
            $pdf->Cell(0, 8, "Tratamiento: " . $r['tratamiento_nombre']); $pdf->Ln(6);
            $pdf->Cell(0, 8, "Estado: " . $r['tratamiento_estado']); $pdf->Ln(6);
            $pdf->MultiCell(0, 8, "Notas: " . ($r['tratamiento_notas'] ?? 'Sin notas'));
            $pdf->Ln(5);
        }
    }

    $pdf->Output();

    limpiarTemporales($tmpFiles);
    exit;
}

respond(['success' => false, 'message' => 'Acción inválida']);
