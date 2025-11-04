<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_POST['firma'])) {
    echo "Acceso denegado o datos faltantes.";
    exit();
}

$id_usuario = $_SESSION['id'];
$nombre_archivo = "firma_" . $id_usuario . "_" . time() . ".png";
$ruta_guardado = "firmas_consentimientos/" . $nombre_archivo;

// Obtener los datos base64 de la imagen
$data = $_POST['firma'];
$data = str_replace('data:image/png;base64,', '', $data);
$data = str_replace(' ', '+', $data);
$datosImagen = base64_decode($data);

// Guardar imagen
if (file_put_contents($ruta_guardado, $datosImagen)) {
    echo "Firma guardada correctamente.";
    // Aquí puedes guardar en base de datos si lo deseas
    // Ejemplo:
    // require '../db.php';
    // $sql = "INSERT INTO consentimiento_firmado (usuario_id, ruta_imagen, fecha) VALUES (?, ?, NOW())";
    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("is", $id_usuario, $nombre_archivo);
    // $stmt->execute();
    // $stmt->close();
} else {
    echo "Error al guardar la firma.";
}
?>
