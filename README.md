# Proyecto ProPiel

Este proyecto es un sistema web para gestión de pacientes, consentimientos, citas, historial y administración general de un consultorio dermatológico.

##  Características principales

* **Gestión de usuarios** con roles (admin, user).
* **Subida y administración de archivos** (PDF, imágenes, consentimientos).
* **Generación de pantallas dinámicas** para usuarios tipo `user` mostrando imágenes en carrusel.
* **Generación automática de pantallas** según la cantidad de imágenes.
* **Lectura de metadata EXIF** para guardar imágenes según año/mes.
* **Sistema de citas** con registro, edición y cancelación.
* **Descarga de consentimientos** y listado de archivos ya generados.
* **Dashboard personalizado** según tipo de usuario.

##  Tecnologías utilizadas

* **PHP 8+**
* **MySQL** (puerto configurado: 3306)
* **HTML5 / CSS3 / JavaScript**
* **Bootstrap** para interfaz
* **FPDF** para generación de PDFs
* **GSAP** y **Tailwind / Bootstrap** en módulos especiales del proyecto

##  Estructura del proyecto

* `/dashboard/` – Panel principal del usuario
* `/admin/` – Subida de archivos e imágenes
* `/files/` – Archivos del sistema
* `/consentimientos/` – PDFs generados
* `/citas/` – Módulo de citas
* `/historial/` – Historial médico y generación de PDF
* `/js/` – scripts globales
* `/css/` – estilos

## ⚙️ Configuración

1. Importa la base de datos incluida en `/database/propiel.sql`.
2. Configura el archivo `db.php` con las credenciales correctas.
3. Asegura permisos de escritura para las carpetas:

   * `/consentimientos/`
   * `/files/`
   * `/upload/`

##  Funcionalidades específicas

###  Para Administradores

* Subir imágenes al sistema
* Generar pantallas dinámicas
* Ver archivos cargados
* Administrar usuarios
* Gestionar citas

###  Para Usuarios

* Ver pantallas dinámicas con carrusel (en grupos de 3 fotos)
* Descargar sus consentimientos generados
* Ver sus citas registradas


##  Autor

LEXIS URIEL LEYVA FERNANDEZ, CHRISTOPHER VARGAS OJENDIZ, YAEL SANCHEZ CORTEZ, JOSÉ ARMANDO AHUELICAN DUARTES – Proyecto ProPiel

##  Licencia

Uso interno y académico para consultorio dermatológico.
