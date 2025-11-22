<?php
    //Importar la libreria fpdf para generar archivos PDF
    require_once '../fpdf/fpdf.php';
    
    //Importar el archivo de conexion a la bd
    require_once '../conexion.php';

    //Obtener los parametros desde la URL usando el ID del paciente y de especialidad
    $id_paciente = $_GET['id'] ?? 0;
    $especialidad = $_GET['especialista'] ?? '';

    //Verificar si se proporcionaron los parametros necesarios
    if(!$id_paciente || !$especialidad){
        die("Faltan parametros para generar el historial");
    }

    //Consulta SQL para obtener los datos del historial medico del paciente por especialidad
    $sql = "SELECT p.nombre, p.fecha_nacimiento, h.fecha, h.diagnostico, h.tratamiento, h.observaciones, e.nombre AS especialista, e.especialidad FROM historial_medico h JOIN pacientes p ON h.id_paciente = p.id JOIN citas c ON c.id_paciente = p.id AND DATE(c.fecha) = h.fecha JOIN especialistas e ON c.id_especialista = e.id WHERE p.id = ? AND e.especialidad = ?";
    
    //Preparar y ejecutar la consulta de forma segura
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $id_paciente, $especialidad);
    $stmt->execute();
    $result = $stmt->get_result();

    //Inicio del documento PDF
    $pdf = new FPDF(); //Creamos la instancia de FPDF (por defecto sera A4)
    $pdf->AddPage(); //Agregar pagina en blanco
    $pdf->SetFont('Arial','B',16); //Establecemos el tipo y formato de letra

    //Titulo centrado con las especilidades en primer letra en mayusculas
    $pdf->Cell(0,10, utf8_decode('Historial Médico -' . ucfirst($especialidad)), 0,1, 'C');
    $pdf->Ln(10); //Salto de linea vertical
    $pdf->SetFont('Arial','',12); //Fuente Arial tamaño 12 sin negritas
    $registro_encontrado=false; //Verificamos si hay resultados

    //Iterar por cada registro del historial medico
    while($row = $result->fetch_assoc()){
        $registro_encontrado = true;
    
    //Formato de fecha dd/mm/YY
    $fecha_formateada = date('d/m/Y', strtotime($row['fecha']));

    //Calcular la edad del paciente
    $fecha_nac = new DateTime($row['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
    $fecha_nac_formateada = $fecha_nac->format('d/m/Y');

    //Imprimir datos generales del paciente
   $pdf->Cell(0,10, utf8_decode("Paciente: " . $row['nombre']), 0, 1);
   $pdf->Cell(0,10, utf8_decode("Fecha de nacimiento: " . $fecha_nac_formateada . " | Edad: " . $edad . " años"), 0, 1);
   $pdf->Cell(0,10, utf8_decode("Médico tratante: " . $row['especialista']), 0, 1);
   $pdf->Cell(0,10, utf8_decode("Fecha: " . $fecha_formateada), 0, 1);

   //Uso de multicell para contenido largo y multilinea
   $pdf->MultiCell(0,10, utf8_decode("Diagnostico: " . $row['diagnostico']));
   $pdf->MultiCell(0,10, utf8_decode("Tratamiento: " . $row['tratamiento']));
   $pdf->MultiCell(0,10, utf8_decode("Observaciones: " . $row['observaciones']));
   $pdf->Ln(5); //Espacio entre registros

   }

   //En caso de no encontrar registrios para el paciente y especialidad
   if(!$registro_encontrado){
      $pdf->Cell(0,10, utf8_decode("No se encontraron entradas del historial Médico para esta Especialidad"), 0,1);
   }

   //Salida del documento PDF (inline)
   $pdf->Output();
?>