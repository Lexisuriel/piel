<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
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
        .btn-pagar:hover {
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
        <div class="mb-3">
            <label for="especialidad" class="form-label">Especialidad:</label>
            <select name="especialidad" id="especialidad" class="form-control" required>
                <option value="">Seleccionar</option>
                <option value="DERMA">Dermatología</option>
                <option value="PODO">Podología</option>
                <option value="TAMIZ">Tamizaje</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de cita:</label>
            <select name="tipo" id="tipo" class="form-control" required>
                <option value="">Seleccionar</option>
                <option value="Primera vez">Primera vez</option>
                <option value="Subsecuente">Subsecuente</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha:</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora:</label>
            <input type="time" name="hora" id="hora" class="form-control" required>
        </div>

        <button type="button" class="btn btn-pagar" onclick="simularPago()">Pagar 50% y Agendar</button>
    </form>

    <div class="horario">
        <h5>🕐 Horarios de Atención</h5>
        <table border="1" class="table table-bordered table-sm">
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
    function simularPago() {
        if (confirm('¿Deseas simular el pago del 50% para agendar la cita?')) {
            document.getElementById('formCita').submit();
        }
    }
</script>

</body>
</html>
