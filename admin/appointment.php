<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Appointment.php';
include_once 'class/Patient.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
if(!$user->loggedIn()) {
    header("Location: index.php");
}
$appointment = new Appointment($db);
$patient = new Patient($db);
include('inc/header4.php');
?>
<script src="js/appointment.js"></script>	
<style>
    /* Estilos generales */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #333;
        padding-top: 50px !important; /* Ajuste clave para el navbar */
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
        width: 16.666667%;
        top: 50px !important;
        left: 0;
        overflow-y: auto;
        padding: 20px 0;
    }
    
    .left_menus .nav-link {
        color: white !important;
        padding: 12px 20px;
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
        margin-left: 16.666667%;
        padding: 25px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        min-height: calc(100vh - 50px) !important;
    }
    
    /* Tabla de citas */
    #appointmentListing {
        margin-top: 20px;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #appointmentListing thead {
        background-color: #2A9D8F;
        color: white;
    }
    
    #appointmentListing th {
        padding: 15px;
        font-weight: 500;
    }
    
    #appointmentListing td {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }
    
    /* Botones */
    #createAppointment {
        background-color: #2A9D8F;
        border-color: #2A9D8F;
        padding: 8px 20px;
    }
    
    #createAppointment:hover {
        background-color: #21867A;
        border-color: #1c7469;
    }
    
    /* Modales */
    .modal-header {
        background-color: #2A9D8F;
        color: white;
        border-radius: 5px 5px 0 0;
    }
    
    .modal-title {
        font-weight: 500;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .left_menus {
            width: 250px;
            transform: translateX(-250px);
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
</style>
</head>
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
                        <a class="nav-link" href="doctor.php"><i class="fa fa-user-md mr-2"></i>Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patient.php"><i class="fa fa-procedures mr-2"></i>Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointment.php"><i class="fa fa-calendar-check mr-2"></i>Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specialization.php"><i class="fa fa-stethoscope mr-2"></i>Specialization</a>
                    </li>
                    <?php if($user->isAdmin()) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php"><i class="fa fa-users mr-2"></i>Users</a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9 col-lg-10 main"> 
                <h2>Manage Appointment</h2> 
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="panel-title"></h3>
                        </div>
                        <?php if($user->isAdmin()) { ?>
                        <div class="col-md-2" align="right">
                            <button type="button" id="createAppointment" class="btn btn-success" title="Create Appointment"><i class="fa fa-plus mr-2"></i>Add</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <table id="appointmentListing" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Doctor</th>                    
                            <th>Specialization</th>    
                            <th>Fee</th>    
                            <th>Appointment Time</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>    
                            <th></th>                    
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        
        <div id="appointmentModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="appointmentForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"></button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Edit Record</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group"
                                <label for="patient_name" class="control-label">Patient</label>
                                <select class="form-control" id="patient_name" name="patient_name"/>
                                <?php 
                                $result = $patient->patientList();
                                while ($patients = $result->fetch_assoc()) {     
                                ?>
                                    <option value="<?php echo $patients['id']; ?>"><?php echo $patients['name']; ?></option>                            
                                <?php } ?>
                                </select>            
                            </div>
                            <div class="form-group"
                                <label for="doctor" class="control-label">Doctor</label>
                                <select class="form-control" id="doctor_name" name="doctor_name"/>
                                <?php 
                                $result = $appointment->doctorList();
                                while ($doctor = $result->fetch_assoc()) {     
                                ?>
                                    <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['name']; ?></option>                            
                                <?php } ?>
                                </select>            
                            </div>
                            <div class="form-group">
                                <label for="specialization" class="control-label">Specialization</label>                            
                                <select class="form-control" id="specialization" name="specialization"/>
                                <?php 
                                $result = $appointment->specializationList();
                                while ($specialization = $result->fetch_assoc()) {     
                                ?>
                                    <option value="<?php echo $specialization['id']; ?>"><?php echo ucfirst($specialization['specialization']); ?></option>                            
                                <?php } ?>
                                </select>                                
                            </div>      
                            <div class="form-group">
                                <label for="fee" class="control-label">Fee</label>                            
                                <input type="text" class="form-control" id="fee" name="fee" placeholder="fee">                            
                            </div>       
                            <div class="form-group">
                                <label for="appointment_date" class="control-label">Appointment Date</label>                            
                                <input type="date" class="form-control"  id="appointment_date" name="appointment_date" value="<?php echo date('d-m-Y'); ?>">                    
                                
                            </div>                        
      
                            <div class="form-group">
                                <label for="appointment_slot" class="control-label">Appointment Slots</label>                            
                                <select class="form-control" id="appointment_slot" name="appointment_slot">
                                </select>        
                            </div>
                            <div class="form-group">
                                <label for="description" class="control-label">Active</label>                            
                                <select class="form-control" id="status" name="status"/>
                                    <option value="Active">Active</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>                        
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" id="id" />
                            <input type="hidden" name="action" id="action" value="" />
                            <input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="appointmentDetails" class="modal fade">
            <div class="modal-dialog">            
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Appointment Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="control-label">Patient Name:</label>
                            <span id="a_patient"></span>    
                        </div>
                        <div class="form-group">
                            <label for="p_gender" class="control-label">Doctor:</label>                
                            <span id="a_doctor"></span>                            
                        </div>       
                        <div class="form-group">
                            <label for="p_age" class="control-label">Specialization:</label>                            
                            <span id="a_special"></span>                                
                        </div>    
                        <div class="form-group">
                            <label for="a_fee" class="control-label">Fee:</label>                            
                            <span id="a_fee"></span>                                
                        </div>    
                        <div class="form-group">
                            <label for="phone" class="control-label">Appoint Date Time:</label>                            
                            <span id="a_time"></span>                    
                        </div>            
                        <div class="form-group">
                            <label for="a_status" class="control-label">Status:</label>                            
                            <span id="a_status"></span>                            
                        </div>
                        
                    </div>                    
                </div>            
            </div>
        </div>
    </div>
</body>
</html>