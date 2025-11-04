<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn()) {
    header("Location: index.php");
}
include('inc/header4.php');
?>
<script src="js/patient.js"></script>	
<style>
    /* Estilos generales */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #333;
        padding-top: 50px !important; /* Ajuste para navbar fijo */
    }
    
    /* Navbar superior compacta */
    .navbar {
        background-color: #B1DFD4 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.4rem 1rem !important;
        height: 50px !important;
        min-height: 50px !important;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
    }
    
    /* Sidebar izquierdo */
    .left_menus {
        background-color: #2A9D8F;
        height: calc(100vh - 50px) !important;
        position: fixed;
        width: 220px; /* Ancho fijo */
        top: 50px !important;
        left: 0;
        overflow-y: auto;
        padding: 15px 0;
    }
    
    .left_menus .nav-link {
        color: white !important;
        padding: 10px 15px;
        margin: 5px 10px;
        border-radius: 4px;
        transition: all 0.3s;
    }
    
    .left_menus .nav-link:hover {
        background-color: rgba(255,255,255,0.15);
    }
      
    .left_menus .nav-link.active {
        background-color: rgba(255,255,255,0.25);
        font-weight: 500;
    }
    
    /* Contenido principal */
    .main {
        margin-left: 220px; /* Igual al ancho del sidebar */
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        min-height: calc(100vh - 50px);
    }
    
    /* Tabla de pacientes */
    #patientListing {
        margin-top: 15px;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    #patientListing thead {
        background-color: #2A9D8F;
        color: white;
    }
    
    #patientListing th {
        padding: 12px 15px;
        font-weight: 500;
    }
    
    #patientListing td {
        padding: 10px 12px;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }
    
    /* Botones */
    #addPatient {
        background-color: #2A9D8F;
        border-color: #2A9D8F;
        padding: 6px 15px;
        font-size: 0.9rem;
    }
    
    #addPatient:hover {
        background-color: #21867A;
        border-color: #1c7469;
    }
    
    /* Modales */
    .modal-header {
        background-color: #2A9D8F;
        color: white;
        border-radius: 5px 5px 0 0;
        padding: 12px 15px;
    }
    #patientListing td:nth-child(8) {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
    
    .modal-title {
        font-weight: 500;
        font-size: 1.1rem;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .left_menus {
            width: 220px;
            transform: translateX(-220px);
            transition: transform 0.3s ease;
        }
        
        .left_menus.active {
            transform: translateX(0);
        }
        
        .main {
            margin-left: 0;
            padding: 15px;
        }
    }
</style></head>
<body>
    <div class="container-fluid">
        <?php include('top_menus.php'); ?>
        <div class="row row-offcanvas row-offcanvas-left">
   <div class="col-md-3 col-lg-2 left_menus">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fa fa-dashboard mr-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctor.php"><i class="fa fa-user-md mr-2"></i> Doctor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patient.php"><i class="fa fa-procedures mr-2"></i>Pacientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointment.php"><i class="fa fa-calendar-check mr-2"></i>Citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specialization.php"><i class="fa fa-stethoscope mr-2"></i>Especializaciones</a>
                    </li>
                    <?php if($_SESSION["role"] == 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php"><i class="fa fa-users mr-2"></i>Usuario ADM</a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt mr-2"></i>Cerrar Sesión</a>
                    </li>
                </ul>
            </div>            
            <div class="col-md-9 col-lg-10 main"> 
                <h2>Administrar Pacientes</h2> 
  <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="panel-title"></h3>
                        </div>
                        <?php if($_SESSION["role"] != 'patient') { ?>
                        <div class="col-md-2" align="right">
                            <button type="button" id="addPatient" class="btn btn-success" title="Add Patient"><i class="fa fa-plus mr-2"></i>Añadir</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <table id="patientListing" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>                    
                            <th>Género</th>    
                            <th>Edad</th>    
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Dirección</th>
                            <th> Historial Medico</th>
                            <th></th>
                            <th></th>    
                            <th></th>                    
                        </tr>
                    </thead>
                </table>
            </div>
        </div>                
        <!-- Modal para Editar/Añadir Paciente -->
        <div id="patientModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="patientForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"></button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Editar Paciente</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="control-label">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>            
                            </div>
                            <div class="form-group">
                                <label for="gender" class="control-label">Género</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select</option>
                                    <option value="Male">Masculino</option>
                                    <option value="Female">Femenino</option>
                                </select>        
                            </div>      
                            <div class="form-group">
                                <label for="birthdate" class="control-label">Fecha de Nacimiento</label>                            
                                <input type="date" class="form-control" id="birthdate" name="birthdate" required>                            
                            </div>
                            <div class="form-group">
                                <label for="age" class="control-label">Edad</label>                            
                                <input type="number" class="form-control" id="age" name="age" readonly>                            
                            </div>       
                            <div class="form-group">
                                <label for="email" class="control-label">Email</label>                            
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>                
                            </div>    
                            <div class="form-group">
                                <label for="mobile" class="control-label">Telefono</label>                            
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" required>            
                            </div>            
                            <div class="form-group">
                                <label for="address" class="control-label">Dirección</label>                            
                                <textarea class="form-control" rows="2" id="address" name="address" required></textarea>                            
                            </div>                        
                            <div class="form-group">
                                <label for="history" class="control-label"> Historial Medico</label>                            
<textarea class="form-control" rows="5" id="medical_history" name="medical_history"></textarea>
</div>    
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" id="id" />
                            <input type="hidden" name="action" id="action" value="" />
                            <input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
        <!-- Modal para Ver Detalles del Paciente -->
<div id="patientDetails" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Información de Pacientes</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name:</label>
                    <p id="p_name"></p>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <p id="p_gender"></p>
                </div>
                <div class="form-group">
                    <label>Age:</label>
                    <p id="p_age"></p>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <p id="p_email"></p>
                </div>
                <div class="form-group">
                    <label>Mobile:</label>
                    <p id="p_mobile"></p>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <p id="p_address"></p>
                </div>
                <div class="form-group">
                    <label>Medical History:</label>
                    <p id="p_history"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        
    </div>
        <script>
    // Actualización para el campo de edad (calculada automáticamente)
    document.getElementById('birthdate').addEventListener('change', function() {
        if(this.value) {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            document.getElementById('age').value = age;
        }
    });
    </script>
</body>
</html>