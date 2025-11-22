<?php
// Librería TCPDF
require_once('../TCPDF-main/tcpdf.php');
// Conexión BD actual
require_once('../db.php');

$db = new Database();
$conn = $db->getConnection();

// Datos del formulario
$id_paciente     = $_POST['id_paciente']    ?? null;
$id_especialista = $_POST['id_especialista'] ?? null;
$firma_img       = $_POST['firma_img']      ?? null;
$id_cita         = $_GET['id_cita']         ?? null;

if (!$id_paciente || !$id_especialista || !$firma_img) {
    die("Datos requeridos faltantes");
}

// ----- OBTENER PACIENTE (TABLA usuario) -----
$stmt = $conn->prepare("SELECT nombre_completo, fecha_nacimiento, tell AS telefono, genero 
                        FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();

if (!$paciente) {
    die("Paciente no encontrado");
}

$nombre_paciente = $paciente['nombre_completo'];
$telefono        = $paciente['telefono'];
$genero          = $paciente['genero'];

// Calcular edad
$edad = 0;
if (!empty($paciente['fecha_nacimiento'])) {
    $fecha_nacimiento = new DateTime($paciente['fecha_nacimiento']);
    $edad = (new DateTime())->diff($fecha_nacimiento)->y;
}

// ----- OBTENER ESPECIALISTA -----
$stmt = $conn->prepare("SELECT nombre, especialidad, cedula FROM especialistas WHERE id = ?");
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$result = $stmt->get_result();
$especialista = $result->fetch_assoc();

if (!$especialista) {
    die("Especialista no encontrado");
}

$nombre_medico = $especialista['nombre'];
$especialidad  = $especialista['especialidad'];
$cedula        = $especialista['cedula'];

// ----- FIRMA (base64 -> PNG temporal) -----
$firma_parts = explode(',', $firma_img);
if (count($firma_parts) < 2) {
    die("Formato de firma no válido");
}
$firma_limpia = $firma_parts[1];

$temp_file = __DIR__ . '/firma_temp.png';
$firma_data = base64_decode($firma_limpia);
if ($firma_data === false) {
    die("Error al decodificar la firma");
}
if (!file_put_contents($temp_file, $firma_data)) {
    die("No se pudo guardar la firma temporal");
}

// ----- CREAR PDF -----
$pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->setPrintHeader(false);
$pdf->AddPage();

// Logo centrado
$imgWidth = 31.75;
$imgHeight = 31.75;
$imgX = ($pdf->getPageWidth() - $imgWidth) / 2;
$imgY = $pdf->getY();
$pdf->Image('../propiel-logo.png', $imgX, $imgY, $imgWidth, $imgHeight, 'PNG');
$pdf->setY($imgY + $imgHeight + 5);

// Título
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'CONSENTIMIENTO INFORMADO', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'DE ATENCIÓN Y PRESCRIPCIÓN MÉDICA DERMATOLÓGICA', 0, 1, 'C');
$pdf->Ln(2);

// Cuerpo
$html = '
<p align="justify">
Yo <b><u>' . htmlspecialchars($nombre_paciente) . '</u></b> autorizo al 
<b><u>' . htmlspecialchars($nombre_medico) . '</u></b>, especialista en 
<b><u>' . htmlspecialchars($especialidad) . '</u></b> con cédula 
<b><u>' . htmlspecialchars($cedula) . '</u></b> como mi médico tratante.
Con mi número actual de teléfono <b><u>' . htmlspecialchars($telefono) . '</u></b> y a la
edad de <b><u>' . $edad . ' años</u></b> de sexo 
<b><u>' . htmlspecialchars($genero) . '</u></b>; acudo a consulta externa de
primera vez. Lo cual manifiesto consciente sin presión y es mi voluntad acudir
con él para mi atención médica.
</p>
<p align="justify">
Para lo cual <i>me interrogará sobre mi enfermedad y comorbilidades, me explorará
el área afectada incluyendo el área genital si fuera necesario, lo cual lo hará
siempre con la presencia de la Enfermera. Así mismo me solicitará estudios de laboratorio
y hasta una biopsia de piel según mi enfermedad, me prescribirá una receta médica en la que se
indicarán los nombres de los medicamentos, forma de uso y tiempo que debo tomarlos, así mismo
si fuera necesario mandará una cita subsecuente para valorar la evolución de mi enfermedad.</i>
</p>
<p align="justify">
Todo lo anterior apegado a la ética, profesionalismo y responsabilidad y con base
en el principio de libertad prescriptiva, de acuerdo a lo establecido en las 
<i>Normas Oficiales Mexicanas aplicables (NOM 001 y NOM 234).</i>
</p>
';
$pdf->writeHTML($html, true, false, true, false, '');

// Nombre del paciente y firma
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, htmlspecialchars($nombre_paciente), 0, 1, 'C');

if (file_exists($temp_file)) {
    $pdf->Image($temp_file, ($pdf->getPageWidth() - 60)/2, $pdf->getY(), 60);
}
$pdf->Ln(35);

// Fecha formateada
$dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];
$dia_semana = $dias[date('w')];
$dia = date('d');
$mes = $meses[(int)date('n')];
$anio = date('Y');
$hora = date('H:i a');
$fecha_hora = "$dia_semana, $dia de $mes de $anio siendo las $hora";

$pdf->Cell(0, 10, 'Zihuatanejo, Guerrero a: ' . $fecha_hora, 0, 1, 'R');

// Función para nombrar archivo
function limpiar_nombre($cadena) {
    $no_permitidas = ['Á','É','Í','Ó','Ú','Ñ','á','é','í','ó','ú','ñ'];
    $permitidas    = ['A','E','I','O','U','N','a','e','i','o','u','n'];
    $cadena = str_replace($no_permitidas, $permitidas, $cadena);
    return preg_replace('/[^A-Za-z0-9_\-]/', '_', $cadena);
}

$nombre_limpio = 'consentimiento_' . limpiar_nombre($nombre_paciente) . '_' . date('Ymd_His') . '.pdf';
$carpeta = __DIR__ . '/pdfs-firmados/';

if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$ruta_guardado = $carpeta . $nombre_limpio;

// Guardar copia en el servidor
$pdf->Output($ruta_guardado, 'F');

// GUARDAR EN BASE DE DATOS - NUEVA TABLA consentimientos_pdf
if ($id_cita) {
    // Insertar registro en la tabla consentimientos_pdf
    $insert_sql = "INSERT INTO consentimientos_pdf (id_paciente, id_especialista, id_cita, nombre_archivo, ruta_archivo, fecha_creacion) 
                   VALUES (?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiiss", $id_paciente, $id_especialista, $id_cita, $nombre_limpio, $ruta_guardado);
    
    if ($insert_stmt->execute()) {
        // Éxito al guardar en BD
        error_log("Consentimiento guardado en BD: " . $nombre_limpio);
    } else {
        // Error al guardar en BD
        error_log("Error al guardar consentimiento en BD: " . $insert_stmt->error);
    }
    $insert_stmt->close();
} else {
    error_log("No se pudo obtener id_cita para guardar consentimiento");
}

// Mostrar PDF al usuario
$pdf->Output($nombre_limpio, 'I');

// Borrar firma temporal
if (file_exists($temp_file)) {
    unlink($temp_file);
}

// Cerrar conexión
$conn->close();
?>