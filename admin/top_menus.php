<nav class="navbar navbar-fixed-top navbar-toggleable-sm navbar-inverse top-nav-bar" style="background-color: #B1DFD4;">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>    
    <div class="navbar-collapse collapse" id="collapsingNavbar">
        <ul class="navbar-nav">
            <li class="nav-item active d-flex align-items-center">
                <img src="images/propiel.png" alt="Doctor" style="height: 40px; margin-right: 10px;">
                <h2><a class="nav-link text-white" href="dashboard.php">PRO-PIEL <span class="sr-only"></span></a></h2>
            </li>       
        </ul> 	  
        <ul class="navbar-nav ml-auto" style="margin-right: 15px;"> <!-- Margen derecho añadido -->
            <?php if(!empty($_SESSION) && $_SESSION["userid"]) { ?>
                <li class="nav-item">
                    <a class="nav-link text-white btn btn-outline-dark border-2 mx-1" href="logout.php">Cerrar Sesión</a>         
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link text-white btn btn-outline-dark border-2 mx-1" href="index.php">Iniciar Sesión</a>         
                </li>
            <?php } ?>
        </ul>	  
    </div>
</nav>