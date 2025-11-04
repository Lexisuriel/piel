<?php 
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn()) {
    header("Location: index.php");
}

include_once 'class/Doctor.php';
include_once 'class/Patient.php';
include_once 'class/Appointment.php';

$doctor = new Doctor($db);
$patient = new Patient($db);
$appointment = new Appointment($db);

include('inc/header4.php');
?>
<title>PRO-PIEL - Dashboard</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/dashboard.css" />
</head>
<body>
  <?php include('top_menus.php'); ?>
  
  <div class="container-fluid main">
  <div class="dashboard-header">
  <div class="doctor-profile-container">
    <img src="images/doctor.jpeg" alt="Foto del Doctor" class="doctor-avatar">
    <div class="doctor-info">
      <h1>Bienvenido Dr. <?php 
        echo htmlspecialchars($_SESSION['first_name'] ?? '') . ' ' . 
            htmlspecialchars($_SESSION['last_name'] ?? ''); 
      ?></h1>
    </div>
  </div>
  <button class="profile-btn">Ver perfil</button>
</div>

  <!-- Tarjetas de resumen -->
  <div class="row summary-cards">
    <div class="col-xl-3 col-lg-6 mb-4">
      <div class="card summary-card bg-primary text-white">
        <div class="card-body">
          <h1 class="card-number"><?php echo $patient->getTotalPatient(); ?></h1>
          <p class="card-text">Total de pacientes</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 mb-4">
      <div class="card summary-card bg-success text-white">
        <div class="card-body">
          <h1 class="card-number">15</h1>
          <p class="card-text">Consultas recientes</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 mb-4">
      <div class="card summary-card bg-warning text-white">
        <div class="card-body">
          <h1 class="card-number"><?php echo $appointment->getTotalApointment(); ?></h1>
          <p class="card-text">Citas programadas</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 mb-4">
      <div class="card summary-card bg-info text-white">
        <div class="card-body">
          <h1 class="card-number">$3500</h1>
          <p class="card-text">Pagos realizados</p>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Tarjetas de menú -->
  <div class="row menu-cards">
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-tachometer mr-2"></i> Dashboard</h5>
          <p class="card-text">Panel principal del sistema</p>
          <a href="dashboard.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-user-md mr-2"></i> Doctors</h5>
          <p class="card-text">Gestión de médicos</p>
          <a href="doctor.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-users mr-2"></i> Patients</h5>
          <p class="card-text">Gestión de pacientes</p>
          <a href="patient.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-calendar mr-2"></i> Appointments</h5>
          <p class="card-text">Gestión de citas</p>
          <a href="appointment.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-stethoscope mr-2"></i> Specialization</h5>
          <p class="card-text">Especializaciones médicas</p>
          <a href="specialization.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-user mr-2"></i> Users</h5>
          <p class="card-text">Gestión de usuarios</p>
          <a href="user.php" class="btn btn-sm btn-outline-primary">Acceder</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-4">
      <div class="card menu-card h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa fa-sign-out mr-2"></i> Logout</h5>
          <p class="card-text">Cerrar sesión</p>
          <a href="logout.php" class="btn btn-sm btn-outline-danger">Salir</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>