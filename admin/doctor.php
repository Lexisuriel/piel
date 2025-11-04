<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn() || ($user->loggedIn() && $_SESSION["role"] == 'patient')) { 
    header("Location: index.php");
}
include_once 'class/Appointment.php';
$appointment = new Appointment($db);
include('inc/header4.php');
?>
<script src="js/doctor.js"></script>	
<style>
    /* Estilos generales */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #333;
        padding-top: 50px !important;
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
        width: 220px;
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
        margin-left: 220px;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        min-height: calc(100vh - 50px);
    }
    
    /* Tabla de doctores */
    #doctorListing {
        margin-top: 15px;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    #doctorListing thead {
        background-color: #2A9D8F;
        color: white;
    }
    
    #doctorListing th {
        padding: 12px 15px;
        font-weight: 500;
    }
    
    #doctorListing td {
        padding: 10px 12px;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }
    
    /* Botones */
    #addDoctor {
        background-color: #2A9D8F;
        border-color: #2A9D8F;
        padding: 6px 15px;
        font-size: 0.9rem;
    }
    
    #addDoctor:hover {
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
    
    .modal-title {
        font-weight: 500;
        font-size: 1.1rem;
    }
    
    .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 8px 12px;
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
                <h2>Manage Doctor</h2> 
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="panel-title"></h3>
                        </div>
                        <div class="col-md-2" align="right">
                            <button type="button" id="addDoctor" class="btn btn-success" title="Add Doctor"><i class="fa fa-plus mr-2"></i>Add</button>
                        </div>
                    </div>
                </div>
                <table id="doctorListing" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>                    
                            <th>Address</th>                    
                            <th>Mobile</th>
                            <th>Fee</th>
                            <th>Specialization</th>    
                            <th></th>
                            <th></th>    
                            <th></th>                    
                        </tr>
                    </thead>
                </table>
            </div>
        </div>        
        <div id="doctorModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="doctorForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"></button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Edit Record</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>            
                            </div>                        
                            <div class="form-group">
                                <label for="fee" class="control-label">Fee</label>                            
                                <input type="text" class="form-control" id="fee" name="fee" placeholder="Fee">            
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
                                <label for="phone" class="control-label">Mobile</label>                            
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile">            
                            </div>    
                            <div class="form-group">
                                <label for="address" class="control-label">Address</label>                            
                                <textarea class="form-control" rows="2" id="address" name="address"></textarea>                            
                            </div>
                            <div class="form-group">
                                <label for="phone" class="control-label">Email</label>                            
                                <input type="text" class="form-control" id="email" name="email" placeholder="Email">            
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
        
        <div id="doctorDetails" class="modal fade">
            <div class="modal-dialog">            
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Doctor Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="control-label">Name:</label>
                            <span id="d_name"></span>    
                        </div>
                        <div class="form-group">
                            <label for="Specialization" class="control-label">Specialization:</label>                
                            <span id="d_specialization"></span>                            
                        </div>       
                        <div class="form-group">
                            <label for="Fee" class="control-label">Fee:</label>                            
                            <span id="d_fee"></span>                                
                        </div>    
                        <div class="form-group">
                            <label for="email" class="control-label">Email:</label>                            
                            <span id="d_email"></span>                                
                        </div>    
                        <div class="form-group">
                            <label for="Mobile" class="control-label">Mobile:</label>                            
                            <span id="d_mobile"></span>                    
                        </div>            
                        <div class="form-group">
                            <label for="address" class="control-label">Address:</label>                            
                            <span id="d_address"></span>                            
                        </div>                    
                    </div>                    
                </div>            
            </div>
        </div>
    </div>
</body>
</html>