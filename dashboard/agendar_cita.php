<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once("../db.php");
$db = new Database();
$conn = $db->getConnection();

// Obtener lista de especialistas
$sql = "SELECT id, nombre, especialidad FROM especialistas ORDER BY especialidad";
$result = $conn->query($sql);
$especialistas = [];
while ($row = $result->fetch_assoc()) {
    $especialistas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h3 {
            color: #2a9d8f;
            margin-bottom: 20px;
        }
        .btn-pagar {
            background-color: #2a9d8f;
            color: white;
            margin-top: 10px;
        }
        .btn-pagar:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        .btn-pagar:hover:not(:disabled) {
            background-color: #21867a;
        }
        .horario {
            margin-top: 30px;
            background-color: #e0f7f4;
            padding: 15px;
            border-radius: 10px;
        }
        .horario table {
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Regresar
    </a>

    <h3>Agendar Cita</h3>

    <form id="formCita" method="POST" action="procesar_cita.php">
        <input type="hidden" name="id_paciente" value="<?php echo $_SESSION['id']; ?>">

        <!-- Especialista -->
        <div class="mb-3">
            <label for="id_especialista" class="form-label">Selecciona un especialista:</label>
            <select name="id_especialista" id="id_especialista" class="form-control" required>
                <option value="">--Seleccionar Especialista--</option>
                <?php foreach ($especialistas as $e): ?>
                    <option value="<?php echo $e['id']; ?>">
                        <?php echo htmlspecialchars($e['nombre'] . " - " . $e['especialidad']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Motivo -->
        <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de la cita:</label>
            <textarea name="motivo" id="motivo" rows="3" class="form-control" placeholder="Describe brevemente el motivo de tu cita..." required></textarea>
        </div>

        <!-- Fecha y hora -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="hora" class="form-label">Hora:</label>
                <input type="time" name="hora" id="hora" class="form-control" required>
            </div>
        </div>

        <!-- Botón deshabilitado hasta que se llenen los campos -->
        <button type="button" class="btn btn-pagar w-100" onclick="verificarDisponibilidad()">Verificar disponibilidad</button>

    </form>

    <div class="horario mt-4">
        <h5>🕐 Horarios de Atención</h5>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Horario</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sábado</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>9:00 - 12:00</td><td></td><td></td><td></td><td></td><td></td><td>DERMA</td></tr>
                <tr><td>12:00 - 14:40</td><td>DERMA</td><td>DERMA</td><td>DERMA</td><td>DERMA</td><td>DERMA</td><td></td></tr>
                <tr><td>16:00 - 18:00</td><td>DERMA</td><td>TAMIZ</td><td>DERMA</td><td>TAMIZ</td><td>DERMA</td><td></td></tr>
                <tr><td>18:00 - 20:00</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>
    </div>
</div>
<script>
function verificarDisponibilidad() {
    const fecha = document.getElementById("fecha").value;
    const hora = document.getElementById("hora").value;
    const id_especialista = document.getElementById("id_especialista").value;

    if (!fecha || !hora || !id_especialista) {
        alert("Por favor, completa todos los campos antes de verificar.");
        return;
    }

    fetch("verificar_cita.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `fecha=${encodeURIComponent(fecha)}&hora=${encodeURIComponent(hora)}&id_especialista=${encodeURIComponent(id_especialista)}`
    })
    .then(response => response.text())
    .then(text => {
        console.log("Respuesta del servidor:", text);
        if (text.trim() === "ocupado") {
            alert("⚠️ Esa fecha y hora ya están ocupadas.");
        } else if (text.trim() === "disponible") {
            alert("✅ Esa fecha y hora están disponibles.");
        } else {
            alert("Error inesperado del servidor → " + text);
        }
    })
    .catch(error => {
        console.error("Error al verificar:", error);
        alert("Error al verificar la cita.");
    });
}
</script>


</body>
</html>