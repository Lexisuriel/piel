<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$nombre = $_SESSION['nombre_completo'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consentimiento Informado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            padding: 30px;
            font-family: Arial, sans-serif;
        }
        .consentimiento-container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .consent-img {
            width: 100%;
            display: block;
        }
        #canvasFirma {
            border: 2px solid #aaa;
            border-radius: 5px;
            width: 100%;
            height: 200px;
            margin-top: 15px;
            cursor: crosshair;
        }
        .btn-group {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    
    <div class="consentimiento-container">
    <a href="dashboard.php" class="btn btn-secondary mb-3">
  <i class="fas fa-arrow-left"></i> Regresar al Dashboard
</a>
        <h3 class="text-center text-success mb-3">Consentimiento Informado</h3>

        <!-- Imagen del consentimiento -->
        <img src="imagenes/consentimiento_info.jpg" alt="Consentimiento" class="consent-img" id="imgFondo">

        <!-- Canvas para firmar -->
        <label class="mt-3">Firma aquí:</label>
        <canvas id="canvasFirma"></canvas>

        <div class="btn-group d-flex justify-content-between">
            <button class="btn btn-secondary" onclick="descargarConsentimiento()">Descargar consentimiento</button>
            <button class="btn btn-danger" onclick="limpiarCanvas()">Limpiar firma</button>
            <button class="btn btn-success" onclick="guardarFirma()">Firmar y Enviar</button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById("canvasFirma");
        const ctx = canvas.getContext("2d");
        let dibujando = false;

        canvas.addEventListener("mousedown", () => dibujando = true);
        canvas.addEventListener("mouseup", () => dibujando = false);
        canvas.addEventListener("mouseout", () => dibujando = false);
        canvas.addEventListener("mousemove", dibujar);

        function dibujar(e) {
            if (!dibujando) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
            ctx.strokeStyle = "black";

            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function limpiarCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function guardarFirma() {
            const firma = canvas.toDataURL();
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "guardar_firma.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                alert(this.responseText);
                if (this.status === 200) {
                    window.location.href = "Dashboard.php";
                }
            };
            xhr.send("firma=" + encodeURIComponent(firma));
        }

        function descargarConsentimiento() {
            const link = document.createElement("a");
            link.href = "imagenes/consentimiento_info.jpg";
            link.download = "consentimiento_informado.jpg";
            link.click();
        }
    </script>
</body>
</html>
