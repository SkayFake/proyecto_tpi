<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../models/expedienteModel.php';

class ExpedienteController {
    private $model;
    
    public function __construct($db) {
        $this->model = new ExpedienteModel($db);
    }
    
    public function mostrarFormulario() {
        include 'views/formulario_busqueda.php';
    }
    
    public function buscarExpediente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_paciente'])) {
            $nombrePaciente = trim($_POST['nombre_paciente']);
            
            if (empty($nombrePaciente)) {
                $error = "Por favor ingrese un nombre de paciente";
                include 'views/formulario_busqueda.php';
                return;
            }
            
            $resultados = $this->model->obtenerExpedientePorNombre($nombrePaciente);
            
            if (empty($resultados)) {
                $error = "No se encontraron resultados para el paciente: " . htmlspecialchars($nombrePaciente);
                include 'views/formulario_busqueda.php';
                return;
            }
            
            // Organizar datos del paciente
            $paciente = [
                'nombre' => $resultados[0]['paciente_nombre'],
                'fecha_nacimiento' => $resultados[0]['fecha_nacimiento'],
                'sexo' => $resultados[0]['sexo'],
                'telefono' => $resultados[0]['telefono'],
                'correo' => $resultados[0]['correo'],
                'direccion' => $resultados[0]['direccion'],
                'dui' => $resultados[0]['dui'],
                'notas' => $resultados[0]['paciente_notas']
            ];
            
            include 'views/expediente_view.php';
        }
    }
    
    public function exportarPDF() {
        if (isset($_GET['nombre'])) {
            $nombrePaciente = $_GET['nombre'];
            $resultados = $this->model->obtenerExpedientePorNombre($nombrePaciente);
            
            if (!empty($resultados)) {
                $this->generarPDF($resultados);
            }
        }
    }
    
    private function generarPDF($datos) {
        require_once('libs/tcpdf/tcpdf.php');
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema Médico');
        $pdf->SetTitle('Expediente Médico');
        $pdf->SetSubject('Expediente del Paciente');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        
        $paciente = $datos[0];
        
        $html = '<h1 style="text-align:center; color:#2c3e50;">EXPEDIENTE MÉDICO</h1>';
        $html .= '<hr>';
        
        // Información del paciente
        $html .= '<h2 style="color:#34495e;">Datos del Paciente</h2>';
        $html .= '<table border="1" cellpadding="5" style="width:100%;">';
        $html .= '<tr><td><strong>Nombre:</strong></td><td>' . htmlspecialchars($paciente['paciente_nombre']) . '</td></tr>';
        $html .= '<tr><td><strong>Fecha de Nacimiento:</strong></td><td>' . htmlspecialchars($paciente['fecha_nacimiento']) . '</td></tr>';
        $html .= '<tr><td><strong>Sexo:</strong></td><td>' . htmlspecialchars($paciente['sexo']) . '</td></tr>';
        $html .= '<tr><td><strong>Teléfono:</strong></td><td>' . htmlspecialchars($paciente['telefono']) . '</td></tr>';
        $html .= '<tr><td><strong>Correo:</strong></td><td>' . htmlspecialchars($paciente['correo']) . '</td></tr>';
        $html .= '<tr><td><strong>Dirección:</strong></td><td>' . htmlspecialchars($paciente['direccion']) . '</td></tr>';
        $html .= '<tr><td><strong>DUI:</strong></td><td>' . htmlspecialchars($paciente['dui']) . '</td></tr>';
        $html .= '<tr><td><strong>Notas:</strong></td><td>' . htmlspecialchars($paciente['paciente_notas']) . '</td></tr>';
        $html .= '</table>';
        
        // Odontogramas
        $html .= '<h2 style="color:#34495e; margin-top:20px;">Historial de Odontogramas</h2>';
        $html .= '<table border="1" cellpadding="5" style="width:100%;">';
        $html .= '<tr style="background-color:#3498db; color:white;">
                    <th>Fecha</th>
                    <th>Observaciones</th>
                  </tr>';
        
        foreach ($datos as $registro) {
            if (!empty($registro['odontograma_fecha'])) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($registro['odontograma_fecha']) . '</td>';
                $html .= '<td>' . htmlspecialchars($registro['odontograma_observaciones']) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        
        // Tratamientos
        $html .= '<h2 style="color:#34495e; margin-top:20px;">Tratamientos</h2>';
        $html .= '<table border="1" cellpadding="5" style="width:100%;">';
        $html .= '<tr style="background-color:#3498db; color:white;">
                    <th>Tratamiento</th>
                    <th>Estado</th>
                    <th>Notas</th>
                  </tr>';
        
        foreach ($datos as $registro) {
            if (!empty($registro['tratamiento_nombre'])) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($registro['tratamiento_nombre']) . '</td>';
                $html .= '<td>' . htmlspecialchars($registro['tratamiento_estado']) . '</td>';
                $html .= '<td>' . htmlspecialchars($registro['tratamiento_notas']) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output('expediente_' . preg_replace('/[^a-zA-Z0-9]/', '_', $paciente['paciente_nombre']) . '.pdf', 'D');
        exit;
    }
}
?>