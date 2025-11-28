<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../models/ExpedienteModel.php';

$exp = new ExpedienteModel();

$action = $_GET['action'] ?? null;

function respond($arr) {
    echo json_encode($arr);
    exit;
}

if ($action === "buscar") {

    $json = json_decode(file_get_contents("php://input"), true);
    $nombre = trim($json['nombre_paciente'] ?? '');

    if ($nombre === '') {
        respond(['success' => false, 'message' => 'Debe ingresar un nombre de paciente']);
    }

    $rows = $exp->obtenerExpedientePorNombre($nombre);

    if (!$rows) {
        respond(['success' => false, 'message' => 'No se encontró expediente del paciente']);
    }

    // Separar paciente / odontogramas / tratamientos
    $paciente = [
        'nombre' => $rows[0]['nombre_paciente1'],
        'fecha_nacimiento' => $rows[0]['fecha_nacimiento'],
        'sexo' => $rows[0]['sexo'],
        'telefono' => $rows[0]['telefono'],
        'correo' => $rows[0]['correo'],
        'direccion' => $rows[0]['direccion'],
        'dui' => $rows[0]['dui'],
        'notas' => $rows[0]['paciente_notas'],
    ];

    $odontogramas = [];
    $tratamientos = [];

    foreach ($rows as $r) {

        if ($r['odontograma_fecha'] !== null) {
            $odontogramas[] = [
                'odontograma_fecha' => $r['odontograma_fecha'],
                'odontograma_observaciones' => $r['odontograma_observaciones'],
                'odontograma_imagen' => $r['odontograma_imagen']
            ];
        }

        if ($r['tratamiento_nombre'] !== null) {
            $tratamientos[] = [
                'tratamiento_nombre' => $r['tratamiento_nombre'],
                'tratamiento_estado' => $r['tratamiento_estado'],
                'tratamiento_notas' => $r['tratamiento_notas'],
            ];
        }
    }

    respond([
        'success' => true,
        'paciente' => $paciente,
        'odontogramas' => $odontogramas,
        'tratamientos' => $tratamientos
    ]);

}

/* ============================================
   PDF
============================================ */

if ($action === "exportar") {

    $nombre = $_GET['nombre'] ?? '';

    if (!$nombre) {
        die("Nombre inválido");
    }

    $rows = $exp->obtenerExpedientePorNombre($nombre);

    if (!$rows) {
        die("No se encontró expediente para exportar");
    }

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Expediente Medico - '.$nombre, 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);

    $p = $rows[0];

    $pdf->Cell(0, 10, 'Fecha de nacimiento: ' . $p['fecha_nacimiento']);
    $pdf->Ln(6);
    $pdf->Cell(0, 10, 'Sexo: ' . $p['sexo']);
    $pdf->Ln(6);
    $pdf->Cell(0, 10, 'Telefono: ' . $p['telefono']);
    $pdf->Ln(6);
    $pdf->Cell(0, 10, 'Correo: ' . $p['correo']);
    $pdf->Ln(6);
    $pdf->Cell(0, 10, 'Direccion: ' . $p['direccion']);
    $pdf->Ln(6);
    $pdf->Cell(0, 10, 'DUI: ' . $p['dui']);
    $pdf->Ln(10);

    // Odontogramas
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 10, 'Odontogramas:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    foreach ($rows as $r) {
        if ($r['odontograma_fecha']) {
            $pdf->Cell(0, 10, 'Fecha: '.$r['odontograma_fecha']);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 8, 'Observaciones: '.$r['odontograma_observaciones']);
            $pdf->Ln(4);
        }
    }

    // Tratamientos
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 10, 'Tratamientos:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    foreach ($rows as $r) {
        if ($r['tratamiento_nombre']) {
            $pdf->Cell(0, 10, 'Tratamiento: '.$r['tratamiento_nombre']);
            $pdf->Ln(6);
            $pdf->Cell(0, 10, 'Estado: '.$r['tratamiento_estado']);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 8, 'Notas: '.$r['tratamiento_notas']);
            $pdf->Ln(4);
        }
    }

    $pdf->Output();
    exit;
}

respond(['success' => false, 'message' => 'Acción inválida']);
