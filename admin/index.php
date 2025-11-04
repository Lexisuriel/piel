
<?php 
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if($user->loggedIn()) {    
    header("Location: dashboard.php");    
}

$loginMessage = '';
if(!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {    
    $user->email = $_POST["email"];
    $user->password = $_POST["password"];    
    if($user->login()) {
        header("Location: dashboard.php");    
    } else {
        $loginMessage = 'Invalid login! Please try again.';
    }
} else {
    $loginMessage = 'Fill all fields.';
}
include('inc/header4.php');
?>
<title>PRO-PIEL</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
<link rel="stylesheet" href="css/dashboard.css" />
<style>
    .soft-input {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    .soft-input:focus {
        border-color: #B1DFD4;
        box-shadow: 0 0 0 0.2rem rgba(177, 223, 212, 0.25);
    }
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
</head>
<body>
  <?php include('top_menus.php'); ?>    
  <div class="container-fluid" id="main">
    <div class="row row-offcanvas row-offcanvas-left">
      <div class="col-md-5 offset-md-3 mt-5">
        <div class="card shadow-sm border-0 rounded-lg">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <img src="images/propiel.png" alt="PRO-PIEL" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
              <h4 class="text-dark">PRO-PIEL</h4>
            </div>
            
            <?php if ($loginMessage != '') { ?>
              <div class="alert alert-danger alert-dismissible fade show"><?php echo $loginMessage; ?></div>                            
            <?php } ?>
            
            <form id="loginform" method="POST" action="">
              <div class="form-group mb-4">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-user text-muted"></i></span>
                  </div>
                  <input type="text" class="form-control soft-input" id="email" name="email" 
                         value="<?php if(!empty($_POST["email"])) { echo $_POST["email"]; } ?>" 
                         placeholder="Email" required>
                </div>
              </div>
              
              <div class="form-group mb-4">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                  </div>
                  <input type="password" class="form-control soft-input" id="password" name="password" 
                         placeholder="Contraseña" required>
                </div>
              </div>
              
              <div class="form-group mt-4">
                <button type="submit" name="login" value="login" class="btn btn-success btn-block py-2 font-weight-bold">
                  INICIAR SESIÓN
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>