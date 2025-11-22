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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consentimiento Informado - Pro-Piel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        /* RESET Y ESTILOS BASE */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #f8f9fa !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* NAVBAR FORZADO */
        .navbar {
            background-color: #2a9d8f !important;
            background: #2a9d8f !important;
            border-bottom: 3px solid #21867a !important;
            padding: 12px 0 !important;
            width: 100% !important;
            position: relative !important;
            z-index: 1000 !important;
        }
        
        .navbar-dark {
            background-color: #2a9d8f !important;
        }
        
        .navbar-brand {
            font-weight: 700 !important;
            font-size: 1.5rem !important;
            color: white !important;
        }
        
        .navbar-text {
            font-weight: 500 !important;
            color: white !important;
        }
        
        /* CONTENEDOR PRINCIPAL */
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 30px;
            flex: 1;
        }
        
        canvas#firma {
            border: 2px solid #2a9d8f;
            border-radius: 10px;
            background-color: #fff;
            cursor: crosshair;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn-primary-custom {
            background-color: #2a9d8f;
            border-color: #2a9d8f;
            color: white;
            font-weight: 500;
        }
        
        .btn-primary-custom:hover {
            background-color: #21867a;
            border-color: #21867a;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .btn-volver {
            background: #5a6d75;
            color: white;
            font-weight: 500;
        }
        
        .btn-volver:hover {
            background: #465559;
            color: white;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        h3, h4 {
            color: #2a9d8f;
        }
        
        .text-justify {
            text-align: justify;
            line-height: 1.6;
        }
        
        .firma-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }
        
        /* FOOTER FORZADO */
        body > footer {
            background-color: #343a40 !important;
            background: #343a40 !important;
            color: white !important;
            padding: 20px 0 !important;
            margin-top: auto !important;
            width: 100% !important;
            position: relative !important;
            border-top: 3px solid #2a9d8f !important;
        }
        
        .bg-dark {
            background-color: #343a40 !important;
        }
        
        footer p {
            margin-bottom: 0 !important;
            color: white !important;
            font-size: 1rem !important;
        }
        
        /* CONTENEDOR FLUIDO PARA NAVBAR */
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin-top: 10px;
            }
            
            canvas#firma {
                width: 100% !important;
                height: 120px !important;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-container {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior - VERSIÓN SIMPLIFICADA -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2a9d8f !important; padding: 12px 0;">
        <div class="navbar-container">
            <a class="navbar-brand" href="../index.php" style="color: white !important;">
                <i class="fas fa-user-md"></i> Pro-Piel
            </a>
            <div class="navbar-nav">
                <span class="navbar-text" style="color: white !important;">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($nombre_paciente) ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <img src="../propiel-logo.png" class="img-fluid" style="max-width: 180px;" alt="logo Propiel">
        </div>
        
        <h3 class="text-center font-weight-bold">Consentimiento Informado</h3>
        <h4 class="text-center pb-3">DE ATENCIÓN Y PRESCRIPCIÓN MÉDICA DERMATOLÓGICA</h4>
        
        <div class="text-center mb-4">
            <a href="javascript:history.back()" class="btn btn-volver">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
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
            </div>
        </div>

        <!-- FORMULARIO CORREGIDO - Se agregó id_cita a la URL -->
        <form target="_blank" onsubmit="return guardarFirma()" action="generar_consentimiento.php?id_cita=<?= $id_cita ?>" method="POST" class="text-center">
            <input type="hidden" name="id_paciente" value="<?= $id_paciente; ?>">
            <input type="hidden" name="id_especialista" value="<?= $id_especialista; ?>">
            <input type="hidden" name="firma_img" id="firma_img">

            <div class="firma-container mb-4">
                <label class="font-weight-bold h5">Firma del Paciente:</label>
                <p class="text-muted small mb-3">Dibuje su firma en el área inferior</p>
                <canvas id="firma" width="300" height="150"></canvas>
                <div class="mt-2">
                    <button type="button" onclick="limpiarFirma()" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-eraser"></i> Limpiar Firma
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="button" onclick="limpiarFirma()" class="btn btn-outline-danger mr-3">
                    <i class="fas fa-eraser"></i> Limpiar Firma
                </button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-file-pdf"></i> Generar PDF con Firma
                </button>
            </div>
            
            <div class="alert alert-info mt-3" role="alert">
                <i class="fas fa-info-circle"></i> Al hacer clic en "Generar PDF", se abrirá una nueva ventana con su consentimiento firmado.
            </div>
        </form>
    </div>

    <!-- Footer - VERSIÓN SIMPLIFICADA -->
    <footer class="bg-dark text-white text-center py-3" style="background-color: #343a40 !important; color: white !important;">
        <div class="container">
            <p class="mb-0" style="color: white !important;">&copy; 2025 Pro-Piel. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        const canvas = document.getElementById('firma');
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let lastX = 0;
        let lastY = 0;

        // Configuración del canvas
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#2a9d8f';

        // Función para obtener posición
        function getPos(canvas, evt) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (evt.clientX - rect.left) * (canvas.width / rect.width),
                y: (evt.clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        // Mouse events
        canvas.addEventListener('mousedown', (e) => {
            drawing = true;
            const pos = getPos(canvas, e);
            [lastX, lastY] = [pos.x, pos.y];
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const pos = getPos(canvas, e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            [lastX, lastY] = [pos.x, pos.y];
        });

        canvas.addEventListener('mouseup', () => {
            drawing = false;
            ctx.beginPath();
        });

        canvas.addEventListener('mouseout', () => {
            drawing = false;
            ctx.beginPath();
        });

        // Touch events para dispositivos móviles
        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const t = e.touches[0];
            const pos = getPos(canvas, t);
            drawing = true;
            [lastX, lastY] = [pos.x, pos.y];
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!drawing) return;
            const t = e.touches[0];
            const pos = getPos(canvas, t);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            [lastX, lastY] = [pos.x, pos.y];
        });

        canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            drawing = false;
            ctx.beginPath();
        });

        function limpiarFirma() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function guardarFirma() {
            const dataURL = canvas.toDataURL('image/png');

            // Verificar si el canvas está vacío
            const emptyCanvas = document.createElement('canvas');
            emptyCanvas.width = canvas.width;
            emptyCanvas.height = canvas.height;
            const emptyCtx = emptyCanvas.getContext('2d');
            emptyCtx.fillStyle = 'white';
            emptyCtx.fillRect(0, 0, emptyCanvas.width, emptyCanvas.height);

            if (dataURL === emptyCanvas.toDataURL()) {
                alert('Por favor, dibuje su firma antes de generar el PDF.');
                return false;
            }

            document.getElementById('firma_img').value = dataURL;
            return true;
        }

        // Limpiar canvas al cargar la página
        window.addEventListener('load', function() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // DIAGNÓSTICO EN CONSOLA
            console.log('=== DIAGNÓSTICO NAVBAR Y FOOTER ===');
            console.log('Navbar encontrado:', document.querySelector('.navbar'));
            console.log('Footer encontrado:', document.querySelector('footer'));
            console.log('Estilo navbar:', document.querySelector('.navbar')?.style.backgroundColor);
            console.log('Estilo footer:', document.querySelector('footer')?.style.backgroundColor);
            
            // Forzar estilos si es necesario
            const navbar = document.querySelector('.navbar');
            const footer = document.querySelector('footer');
            
            if (navbar) {
                navbar.style.backgroundColor = '#2a9d8f';
                navbar.style.color = 'white';
            }
            
            if (footer) {
                footer.style.backgroundColor = '#343a40';
                footer.style.color = 'white';
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>