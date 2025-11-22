<?php
require_once '../db.php';

// Validar id_cita
$id_cita = $_GET['id_cita'] ?? null;
if (!$id_cita) {
    die("Faltan datos de la cita.");
}

// Conexión
$db = new Database();
$conn = $db->getConnection();

// Obtener datos de la cita + paciente (usuario) + especialista
$sql = "SELECT 
            c.id,
            u.id AS id_paciente,
            u.nombre_completo,
            u.fecha_nacimiento,
            u.tell AS telefono,
            u.genero,
            e.id AS id_especialista,
            e.nombre AS nombre_especialista,
            e.especialidad,
            e.cedula
        FROM citas c
        JOIN usuario u ON c.id_paciente = u.id
        JOIN especialistas e ON c.id_especialista = e.id
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cita);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

if (!$datos) {
    die("No se encontraron datos para esta cita.");
}

$id_paciente      = $datos['id_paciente'];
$id_especialista  = $datos['id_especialista'];
$nombre_paciente  = $datos['nombre_completo'];
$fecha_nac        = $datos['fecha_nacimiento'];
$telefono         = $datos['telefono'];
$genero           = $datos['genero'];
$nombre_medico    = $datos['nombre_especialista'];
$especialidad     = $datos['especialidad'];
$cedula           = $datos['cedula'];

// Calcular edad
$edad = 0;
if (!empty($fecha_nac)) {
    $nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consentimiento Informado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        canvas#firma {
            border: 1px solid #000;
            background-color: #fff;
        }
    </style>
</head>
<body class="container mt-5">
    <div class="text-center">
        <img src="../propiel-logo.png" class="img-fluid w-25" style="max-width: 180px;" alt="logo Propiel">
    </div>
    <h3 class="text-center font-weight-bold">Consentimiento Informado</h3>
    <h4 class="text-center pb-3">DE ATENCIÓN Y PRESCRIPCIÓN MÉDICA DERMATOLÓGICA</h4>
    <div class="text-center mb-4">
    <a href="javascript:history.back()" class="btn btn-secondary">
        ⬅ Regresar
    </a>
</div>

    <p class="text-justify">
        Yo <span class="font-weight-bold"><u><?= htmlspecialchars($nombre_paciente) ?></u></span> autorizo al 
        <span class="font-weight-bold"><u><?= htmlspecialchars($nombre_medico) ?></u></span> especialista en 
        <span class="font-weight-bold"><u><?= htmlspecialchars($especialidad) ?></u></span> con cédula 
        <span class="font-weight-bold"><u><?= htmlspecialchars($cedula) ?></u></span> como mi médico tratante.
        Con mi número actual de teléfono 
        <span class="font-weight-bold"><u><?= htmlspecialchars($telefono) ?></u></span> y a la edad de 
        <span class="font-weight-bold"><u><?= $edad ?> años</u></span> de sexo 
        <span class="font-weight-bold"><u><?= htmlspecialchars($genero) ?></u></span>; acudo a consulta externa de primera vez. 
        Lo cual manifiesto consciente, sin presión y es mi voluntad acudir con él para mi atención médica.
    </p>

    <p class="text-justify">
        Para lo cual <span class="font-italic">me interrogará sobre mi enfermedad y comorbilidades, 
        me explorará el área afectada incluyendo el área genital si fuera necesario, lo cual lo hará
        siempre con la presencia de la Enfermera. Así mismo me solicitará estudios de laboratorio
        y hasta una biopsia de piel según mi enfermedad, me prescribirá una receta médica en la que
        se indicarán los nombres de los medicamentos, forma de uso y tiempo que debo tomarlos,
        así mismo si fuera necesario mandará una cita subsecuente para valorar la evolución de mi enfermedad.</span>
    </p>

    <p class="text-justify">
        Todo lo anterior apegado a la ética, profesionalismo y responsabilidad y con base en el principio
        de libertad prescriptiva, de acuerdo a lo establecido en las 
        <span class="font-italic">Normas Oficiales Mexicanas aplicables (NOM 001 y NOM 234).</span>
    </p>

    <!-- 🔥 FORMULARIO CORREGIDO - Se agregó id_cita a la URL -->
    <form target="_blank" onsubmit="return guardarFirma()" action="generar_consentimiento.php?id_cita=<?= $id_cita ?>" method="POST" class="text-center">
        <input type="hidden" name="id_paciente" value="<?= $id_paciente; ?>">
        <input type="hidden" name="id_especialista" value="<?= $id_especialista; ?>">
        <input type="hidden" name="firma_img" id="firma_img">

        <label class="font-weight-bold">Firma del Paciente:</label><br>
        <canvas id="firma" width="300" height="150"></canvas><br>

        <div class="d-flex justify-content-center mt-3">
            <button type="button" onclick="limpiarFirma()" class="btn btn-danger mr-2">Limpiar Firma</button>
            <button type="submit" class="btn btn-success">Generar PDF</button>
        </div>
    </form>

    <script>
        const canvas = document.getElementById('firma');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        // Mouse
        canvas.addEventListener('mousedown', e => {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        });

        canvas.addEventListener('mousemove', e => {
            if (!drawing) return;
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        });

        canvas.addEventListener('mouseup', () => {
            drawing = false;
            ctx.closePath();
        });

        canvas.addEventListener('mouseleave', () => {
            drawing = false;
            ctx.closePath();
        });

        // Touch
        canvas.addEventListener('touchstart', e => {
            e.preventDefault();
            const t = e.touches[0];
            const pos = getTouchPos(canvas, t);
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });

        canvas.addEventListener('touchmove', e => {
            e.preventDefault();
            if (!drawing) return;
            const t = e.touches[0];
            const pos = getTouchPos(canvas, t);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });

        canvas.addEventListener('touchend', e => {
            e.preventDefault();
            drawing = false;
            ctx.closePath();
        });

        function getTouchPos(canvas, touch) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (touch.clientX - rect.left) * (canvas.width / rect.width),
                y: (touch.clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function limpiarFirma() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function guardarFirma() {
            const dataURL = canvas.toDataURL('image/png');

            const emptyCanvas = document.createElement('canvas');
            emptyCanvas.width = canvas.width;
            emptyCanvas.height = canvas.height;

            if (dataURL === emptyCanvas.toDataURL()) {
                alert('Por favor, dibuje una firma antes de enviar.');
                return false;
            }

            document.getElementById('firma_img').value = dataURL;
            return true;
        }
    </script>
</body>
</html>