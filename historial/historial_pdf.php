<?php
require '../db.php';
require '../fpdf/fpdf.php';

// Obtener parámetros
$id_paciente = $_GET['id_paciente'] ?? null;
$id_especialista = $_GET['id_especialista'] ?? null;

if (!$id_paciente || !$id_especialista) {
    die("Faltan parámetros.");
}

// Consultar datos del historial
$stmt = $conn->prepare("
    SELECT h.fecha, h.diagnostico, h.tratamiento, h.observaciones,
           u.nombre_completo AS paciente,
           e.nombre AS especialista, e.especialidad
    FROM historial_medico h
    JOIN usuario u ON h.id_paciente = u.id
    JOIN especialistas e ON h.id_especialista = e.id
    WHERE h.id_paciente = ? AND h.id_especialista = ?
    ORDER BY h.fecha DESC LIMIT 1
");
$stmt->bind_param("ii", $id_paciente, $id_especialista);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("No existe historial médico para este paciente.");
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// Título
$pdf->SetFont('Helvetica', 'B', 20);
$pdf->Cell(0, 12, utf8_decode('Historial Médico - PROPIEL'), 0, 1, 'C');
$pdf->Ln(12);

// Datos generales
$pdf->SetFont('Helvetica', '', 14);

$pdf->Cell(0, 10, utf8_decode('Paciente: ' . $data['paciente']), 0, 1);
$pdf->Ln(5);

$pdf->Cell(0, 10, utf8_decode('Especialista: ' . $data['especialista'] . ' (' . $data['especialidad'] . ')'), 0, 1);
$pdf->Ln(5);

$pdf->Cell(0, 10, utf8_decode('Fecha de registro: ' . $data['fecha']), 0, 1);
$pdf->Ln(12);

// Sección Diagnóstico
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->Cell(0, 10, utf8_decode('Diagnóstico:'), 0, 1);
$pdf->Ln(3);

$pdf->SetFont('Helvetica', '', 14);
$pdf->MultiCell(0, 9, utf8_decode($data['diagnostico']));
$pdf->Ln(12);

// Sección Tratamiento
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->Cell(0, 10, utf8_decode('Tratamiento:'), 0, 1);
$pdf->Ln(3);

$pdf->SetFont('Helvetica', '', 14);
$pdf->MultiCell(0, 9, utf8_decode($data['tratamiento']));
$pdf->Ln(12);

// Sección Observaciones
$pdf->SetFont('Helvetica', 'B', 15);
$pdf->Cell(0, 10, utf8_decode('Observaciones:'), 0, 1);
$pdf->Ln(3);

$pdf->SetFont('Helvetica', '', 14);
$pdf->MultiCell(0, 9, utf8_decode($data['observaciones']));
$pdf->Ln(10);

$pdf->Output();
