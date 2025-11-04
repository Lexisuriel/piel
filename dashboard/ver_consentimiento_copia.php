<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
// Obtener datos del paciente desde la base de datos
require_once '../db.php'; // Asegúrate de tener tu archivo de conexión

$id_paciente = $_SESSION['id'];
$query = "SELECT * FROM usuario WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();

// Calcular edad a partir de la fecha de nacimiento
$fecha_nac = new DateTime($paciente['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($fecha_nac)->y;

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $datos_consentimiento = [
        'tipo_consulta' => $_POST['tipo_consulta'],
        'diagnostico' => $_POST['diagnostico'],
        'procedimiento' => $_POST['procedimiento'],
        'beneficios' => $_POST['beneficios'],
        'riesgos' => $_POST['riesgos'],
        'alternativas' => $_POST['alternativas'],
        'firma' => $_POST['firma_data'],
        'fecha' => date('Y-m-d H:i:s'),
        'id_paciente' => $id_paciente
    ];
    
    // Guardar en la base de datos (necesitarías crear esta tabla)
    $query = "INSERT INTO consentimientos (id_paciente, tipo_consulta, diagnostico, procedimiento, beneficios, riesgos, alternativas, firma, fecha) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssssss", 
        $datos_consentimiento['id_paciente'],
        $datos_consentimiento['tipo_consulta'],
        $datos_consentimiento['diagnostico'],
        $datos_consentimiento['procedimiento'],
        $datos_consentimiento['beneficios'],
        $datos_consentimiento['riesgos'],
        $datos_consentimiento['alternativas'],
        $datos_consentimiento['firma'],
        $datos_consentimiento['fecha']
    );
    $stmt->execute();
    
    // Generar PDF
    generarPDF($datos_consentimiento, $paciente, $edad);
}

function generarPDF($datos, $paciente, $edad) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Consentimiento Informado</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .firma-container { margin-top: 50px; }
            .firma-img { max-width: 200px; border-top: 1px solid #000; padding-top: 10px; }
        </style>
    </head>
    <body>
        <h2 style="text-align: center;">CONSENTIMIENTO INFORMADO</h2>
        <h3 style="text-align: center;">De atención y prescripción médica dermatológica</h3>
        <p>Yo <strong>'.$paciente['nombre_completo'].'</strong> autorizo al Dr. Hugo Alarcón Hernández especialista en Dermatología con cédula 0018576 como mi médico tratante de mi (y/o) familia _____________________ edad: <strong>'.$edad.'Años </strong> sexo: __________ y número de teléfono: <strong>'.$paciente['tell'].'</strong> que acudo a consulta externa de <strong>'.$datos['tipo_consulta'].'</strong> de Dermatología, lo cual manifiesto consciente sin presión y es mi voluntad acudir con él para mi atención médica.</p>
        <p>Para lo cual me interrogará sobre mi enfermedad y comorbilidades, me explorará, el área afectada incluyendo el área genital si fuera necesario, lo cual lo hará siempre con la presencia de la Enfermera. Así mismo me solicitará estudios de laboratorio y hasta una biopsia de piel según mi enfermedad, me préscribirá una receta médica en la que se indicaran los nombre de los medicamentos, forma de uso y tiempo que debo tomarlos así mismo si fuera necesario mandara una cita subsecuente para valorar la evolución de mi enfermedad. Todo lo anterior apegado a la ética, profesionalismo y
        responsabilidad y con base en el principio de libertad prescriptiva, de acuerdo a lo establecido en las normas oficiales Mexicanas aplicables (Nom 001 y Nom 234).</p>
        <p>Así mismo tiene la responsabilidad de explicarme sobre el diagnóstico y la forma de tratamiento, la prescripción y algunos de los efectos secundarios que pudieran presentarse durante el tratamiento, aclarándome qué derivado del tratamiento pudiera presentarse una reacción alérgica y/o de contacto. Lo cual no es posible saberlo antes de aplicar o tomar el tratamiento. que todos los organismos reaccionan diferente y esto lo excluye de cualquier responsabilidad, ya que lo hace con base en su conocimiento y experiencia médica, que no es su intención causar algún daño colateral, para lo cual se señala lo siguiente.</p>
        <div class="firma-container">
            <p>Nombre y firma del paciente: <strong>'.$paciente['nombre_completo'].'</strong></p>
            <img src="'.$datos['firma'].'" class="firma-img">
        </div>
        
        <p>Lugar: Zihuatanejo,Gro. Fecha: '.date('d/m/Y').' Hora: '.date('H:i').'</p>
    </body>
    </html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("consentimiento_".$paciente['nombre_completo']."_".date('Ymd').".pdf");
}
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
        }
        .form-control, .form-select {
            border-radius: 0;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: #86b7fe;
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
        .consent-text {
            white-space: pre-line;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="consentimiento-container">
        <a href="dashboard.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Regresar al Dashboard
        </a>
        <h3 class="text-center text-success mb-3">Consentimiento Informado</h3>

        <form method="POST" id="formConsentimiento">
            <div class="mb-3">
                <label class="form-label">Yo</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($paciente['nombre_completo']); ?>" readonly>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Edad</label>
                    <input type="text" class="form-control" value="<?php echo $edad; ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sexo</label>
                    <select class="form-select">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($paciente['tell']); ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de consulta</label>
                <select class="form-select" name="tipo_consulta" required>
                    <option value="">Seleccione...</option>
                    <option value="primera vez">Primera vez</option>
                    <option value="subsecuente">Subsecuente</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Diagnóstico principal</label>
                <input type="text" class="form-control" name="diagnostico" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Procedimiento propuesto</label>
                <textarea class="form-control" name="procedimiento" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Beneficios</label>
                <textarea class="form-control" name="beneficios" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Riesgos</label>
                <textarea class="form-control" name="riesgos" rows="2" required>Reacción a cualquiera de los componentes del tratamiento</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Alternativas de manejo diagnóstico o de tratamiento</label>
                <textarea class="form-control" name="alternativas" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Firma del paciente</label>
                <canvas id="canvasFirma"></canvas>
                <input type="hidden" name="firma_data" id="firmaData">
                <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="limpiarCanvas()">Limpiar firma</button>
            </div>

            <div class="btn-group d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="previsualizar()">Previsualizar</button>
                <button type="submit" class="btn btn-success">Firmar y Generar PDF</button>
            </div>
        </form>
    </div>

    <!-- Modal para previsualización -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Previsualización del Consentimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Aquí se cargará el contenido del consentimiento -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('formConsentimiento').submit()">Generar PDF</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const canvas = document.getElementById("canvasFirma");
        const ctx = canvas.getContext("2d");
        let dibujando = false;

        // Ajustar tamaño del canvas para alta resolución
        function resizeCanvas() {
            const ratio = window.devicePixelRatio || 1;
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.style.width = canvas.offsetWidth + 'px';
            canvas.style.height = canvas.offsetHeight + 'px';
            ctx.scale(ratio, ratio);
        }
        
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Eventos para dibujar
        canvas.addEventListener("mousedown", iniciarFirma);
        canvas.addEventListener("touchstart", iniciarFirma);
        canvas.addEventListener("mousemove", dibujar);
        canvas.addEventListener("touchmove", dibujar);
        canvas.addEventListener("mouseup", detenerFirma);
        canvas.addEventListener("touchend", detenerFirma);
        canvas.addEventListener("mouseout", detenerFirma);

        function iniciarFirma(e) {
            e.preventDefault();
            dibujando = true;
            const pos = obtenerPosicion(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function dibujar(e) {
            e.preventDefault();
            if (!dibujando) return;
            
            const pos = obtenerPosicion(e);
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
            ctx.strokeStyle = "#000";
            
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function detenerFirma() {
            dibujando = false;
            // Guardar la firma como imagen
            document.getElementById("firmaData").value = canvas.toDataURL();
        }

        function obtenerPosicion(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;
            
            if (e.type.includes("touch")) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            
            return { x, y };
        }

        function limpiarCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById("firmaData").value = "";
        }

        function previsualizar() {
            const form = document.getElementById("formConsentimiento");
            const formData = new FormData(form);
            
            // Validar que todos los campos estén completos
            let valid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!valid) {
                alert("Por favor complete todos los campos requeridos.");
                return;
            }
            
            if (!document.getElementById("firmaData").value) {
                alert("Por favor proporcione su firma.");
                return;
            }
            
            // Crear contenido de previsualización
            let previewHTML = `
                <h4 class="text-center">CONSENTIMIENTO INFORMADO</h4>
                <p>Yo <strong>${formData.get('nombre_completo') || '<?php echo htmlspecialchars($paciente['nombre_completo']); ?>'}</strong> autorizo al<br>
                Dr. Hugo Alarcón Hernández especialista en Dermatología con cédula 0018576<br>
                como mi médico tratante de mi (y/o) familia______<br>
                edad: <strong>${formData.get('edad') || '<?php echo $edad; ?>'}</strong> sexo: ______ y número de teléfono: <strong>${formData.get('tell') || '<?php echo htmlspecialchars($paciente['tell']); ?>'}</strong> que<br>
                acudo a consulta externa de <strong>${formData.get('tipo_consulta')}</strong> de Dermatología...</p>
                
                <p><strong>Diagnóstico principal:</strong> ${formData.get('diagnostico')}</p>
                <p><strong>Procedimiento propuesto:</strong> ${formData.get('procedimiento')}</p>
                <p><strong>Beneficios:</strong> ${formData.get('beneficios')}</p>
                <p><strong>Riesgos:</strong> ${formData.get('riesgos')}</p>
                <p><strong>Alternativas:</strong> ${formData.get('alternativas')}</p>
                
                <div class="mt-4">
                    <p>Nombre y firma del paciente: <strong>${formData.get('nombre_completo') || '<?php echo htmlspecialchars($paciente['nombre_completo']); ?>'}</strong></p>
                    <img src="${document.getElementById("firmaData").value}" style="max-width: 200px; border-top: 1px solid #000; padding-top: 10px;">
                </div>
            `;
            
            document.getElementById("previewContent").innerHTML = previewHTML;
            
            // Mostrar modal
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }
    </script>
</body>
</html>