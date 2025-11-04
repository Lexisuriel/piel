<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("db.php");
$isLoggedIn = isset($_SESSION['id']);
$userRole = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro-Piel</title>
    <link rel="icon" href="ico/logo.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_login.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        .modal-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
        }

        .custom-modal-info {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
            width: 300px;
            z-index: 9999;
            text-align: center;
        }

        .custom-modal-info h2 {
            color: #2a9d8f;
        }

        .custom-modal-info p {
            color: #444;
            margin-bottom: 20px;
        }

        .custom-modal-info button {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #ccc;
        }

        .footer {
            background-color: #2a9d8f; /* Verde institucional */
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 0.9em;
            position: relative;
            width: 100%;
            z-index: 1;
        }

        .services {
            margin-bottom: 50px;
            position: relative;
            z-index: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .service-card {
            width: 300px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="app">
    <header-component></header-component>

    <main>
        <main-title-component></main-title-component>
        <section class="services">
            <service-card-component
                v-for="service in services"
                :key="service.id"
                :title="service.title"
                :description="service.description"
                :image="service.image"
                @mostrar-info="mostrarModal">
            </service-card-component>
        </section>
    </main>

    <!-- Modal personalizado -->
    <div v-if="modalVisible">
        <div class="modal-bg" @click="cerrarModal"></div>
        <div class="custom-modal-info">
            <h2>{{ modalTitulo }}</h2>
            <p>{{ modalContenido }}</p>
            <button @click="cerrarModal">Cerrar</button>
        </div>
    </div>

    <footer-component></footer-component>

    <!-- Modal Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control custom-input" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control custom-input" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 custom-button">Iniciar Sesión</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <small class="text-muted">¿No tienes cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Regístrate aquí</a></small>
                </div>
            </div>
        </div>
    </div>
<?php
if (isset($_SESSION['registration_errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['registration_errors'] as $error) {
        echo '<p>'.$error.'</p>';
    }
    echo '</div>';
    unset($_SESSION['registration_errors']);
}

// También puedes rellenar automáticamente los campos del formulario con los datos previos
if (isset($_SESSION['form_data'])) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const formData = '.json_encode($_SESSION['form_data']).';
        for (const key in formData) {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) input.value = formData[key];
        }
    });
    </script>';
    unset($_SESSION['form_data']);
}
?>
    <!-- Modal Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Registrarse</h5>
            </div>
            <div class="modal-body">
                <form action="register.php" method="POST">
                    <!-- Campos existentes -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control custom-input" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control custom-input" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control custom-input" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control custom-input" id="confirm-password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control custom-input" id="telefono" name="telefono" required>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control custom-input" id="birthdate" name="birthdate" required>
                    </div>
                    
                    <!-- NUEVOS CAMPOS PARA EL ADMIN -->
                    <div class="mb-3">
                        <label for="gender" class="form-label">Género</label>
                        <select class="form-control custom-input" id="gender" name="gender" required>
                            <option value="">Seleccionar</option>
                            <option value="Male">Masculino</option>
                            <option value="Female">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <textarea class="form-control custom-input" id="address" name="address" rows="2" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 custom-button">Registrarse</button>
                </form>
            </div>
            <div class="modal-footer">
                <small class="text-muted">¿Olvidaste tu contraseña? <a href="#">Recupérala aquí</a></small>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const { createApp, ref } = Vue;

const HeaderComponent = {
    template: `<?php ob_start(); ?>
        <header class="header">
            <div class="logo">
                <img src="logo.png" alt="Pro-Piel Logo">
            </div>
            <button class="menu-btn" @click="toggleMenu">&#9776;</button>
            <nav class="nav" :class="{ 'nav-open': isMenuOpen }">
                <a href="#dermatologia">Dermatología</a>
                <a href="#podologia">Podología</a>
                <a href="#Tamizaje">Tamizaje</a>
                <?php if ($isLoggedIn) : ?>
                    <a href="dashboard/dashboard.php">Dashboard</a>
                    <a href="dashboard/logout.php">Cerrar Sesión</a>
                <?php else : ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </header>
    <?php echo str_replace("`", "\\`", ob_get_clean()); ?>`,
    data() {
        return { isMenuOpen: false };
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        }
    },
    mounted() {
        gsap.from(".header", { y: -100, opacity: 0, duration: 1 });
    }
};

const MainTitleComponent = {
    template: `
        <section class="main-title text-center my-4">
            <h2>Especialistas en Cuidado de la Piel</h2>
            <p>Ofrecemos tratamientos avanzados y cuidados profesionales en dermatología y podología.</p>
        </section>
    `,
    mounted() {
        gsap.from(".main-title", { opacity: 0, duration: 1, delay: 0.5 });
    }
};

const ServiceCardComponent = {
    props: ['title', 'description', 'image'],
    template: `
        <div class="service-card text-center p-3 shadow rounded bg-white">
            <img :src="image" :alt="title" class="img-fluid mb-2 rounded" />
            <h3>{{ title }}</h3>
            <p>{{ description }}</p>
            <button class="btn text-white" style="background-color: #2dd4bf;" @click="$emit('mostrar-info', title)">Más información</button>
        </div>
    `,
    mounted() {
        gsap.from(".service-card", { y: 50, duration: 1, stagger: 0.3 });
    }
};

const FooterComponent = {
    template: `
        <footer class="footer">
            <p>© <?php echo date("Y"); ?> Pro-Piel. Todos los derechos reservados.</p>
            <p>Contáctanos: info@propiel.com | Tel: 123-456-7890</p>
            <p>Síguenos en redes sociales</p>
        </footer>
    `,
    mounted() {
        gsap.from(".footer", { y: 50, opacity: 0, duration: 1, delay: 1 });
    }
};

createApp({
    components: {
        HeaderComponent,
        MainTitleComponent,
        ServiceCardComponent,
        FooterComponent
    },
    setup() {
        const services = ref([
            { id: 1, title: 'Dermatología', description: 'Tratamientos avanzados para el cuidado y la salud de la piel.', image: 'dermatologia.jpg' },
            { id: 2, title: 'Podología', description: 'Servicios especializados en el cuidado de tus pies.', image: 'podologia.jpg' },
            { id: 3, title: 'Tamizaje', description: 'Procedimientos estéticos con resultados profesionales.', image: 'tamizaje.jpg' }
        ]);

        const modalVisible = ref(false);
        const modalTitulo = ref('');
        const modalContenido = ref('');

        const infoMap = {
    'Dermatología': 'Contamos con atención especializada en enfermedades de la piel como acné, manchas (melasma), dermatitis, psoriasis, lunares sospechosos y cáncer de piel. También realizamos procedimientos estéticos como rejuvenecimiento facial, relleno de líneas de expresión, BB Glow, y tratamiento de alopecia. El Dr. Hugo Alarcón, dermatólogo certificado con estudios internacionales, está al frente de este servicio.',

    'Podología': 'El servicio de podología está orientado a pacientes diabéticos y público en general. Se tratan uñas encarnadas (onicocriptosis), hongos, callosidades, verrugas, pie diabético, entre otros. Utilizamos instrumental esterilizado y técnicas especializadas para el manejo seguro de los pies.',

    'Tamizaje': 'Ofrecemos tamizaje neonatal metabólico y auditivo para la detección oportuna de hasta 140 enfermedades, como hipotiroidismo congénito, galactosemia, hipoacusia y sordera congénita. El servicio se realiza con personal capacitado y equipos certificados para asegurar resultados confiables.'
};


        const mostrarModal = (titulo) => {
            modalTitulo.value = titulo;
            modalContenido.value = infoMap[titulo] || 'Información no disponible.';
            modalVisible.value = true;
        };

        const cerrarModal = () => {
            modalVisible.value = false;
        };

        return {
            services,
            modalVisible,
            modalTitulo,
            modalContenido,
            mostrarModal,
            cerrarModal
        };
    }
}).mount('#app');
</script>
</body>
</html>
