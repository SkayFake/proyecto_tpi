<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>DR. MUELAS</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo APP_URL; ?>app/views/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo APP_URL; ?>app/views/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="<?php echo APP_URL; ?>app/views/assets/css/main.css" rel="stylesheet">
  
</head>

<body class="index-page">

  <header id="header" class="header sticky-top">
    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="d-none d-md-flex align-items-center">
          <i class="bi bi-clock me-1"></i> Lunes - Sábado, 8AM a 10PM
        </div>
        <div class="d-flex align-items-center">
          <i class="bi bi-phone me-1"></i> Llámanos +503 6205 7666
        </div>
      </div>
    </div>

    <div class="branding d-flex align-items-center">
      <div class="container position-relative d-flex align-items-center justify-content-end">
        <a href="index.html" class="logo d-flex align-items-center me-auto">
          <img src="<?php echo APP_URL; ?>app/views/assets/img/logo.png" alt="">
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Inicio</a></li>
            <li><a href="#about">Sobre Nosotros</a></li>
            <li><a href="#services">Servicios</a></li>
            <li><a href="#department">Departamentos</a></li>
            <li><a href="#doctors">Doctores</a></li>

            <li class="dropdown"><a href="#"><span>Pacientes</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoPaciente" data-bs-toggle="modal" data-bs-target="#modalPaciente">Agregar Paciente</a></li>
                <li><a href="#" id="btnVerPaciente" data-bs-toggle="modal" data-bs-target="#modalPacienteVer">Ver Pacientes</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Odontólogos</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoOdontologo" data-bs-toggle="modal" data-bs-target="#modalOdontologo">Agregar Odontólogo</a></li>
                <li><a href="#" id="btnVerOdontologo" data-bs-toggle="modal" data-bs-target="#modalOdontologoVer">Ver Odontólogos</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Citas</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevaCita" data-bs-toggle="modal" data-bs-target="#modalCita">Agregar Cita</a></li>
                <li><a href="#" id="btnVerCitas" data-bs-toggle="modal" data-bs-target="#modalCitaVer">Ver Citas</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Odontogramas</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoOdontograma" data-bs-toggle="modal" data-bs-target="#modalOdontograma">Agregar Odontograma</a></li>
                <li><a href="#" id="btnVerOdontogramas" data-bs-toggle="modal" data-bs-target="#modalOdontogramaVer">Ver Odontogramas</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Servicios</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoServicio" data-bs-toggle="modal" data-bs-target="#modalServicio">Agregar Servicios</a></li>
                <li><a href="#" id="btnVerServicios" data-bs-toggle="modal" data-bs-target="#modalServicioVer">Ver Servicios</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Disponibilidad</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevaDisponibilidad" data-bs-toggle="modal" data-bs-target="#modalDisponibilidad">Agregar Disponibilidad</a></li>
                <li><a href="#" id="btnVerDisponibilidad" data-bs-toggle="modal" data-bs-target="#modalDisponibilidadVer">Ver Disponibilidad</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Pagos</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoPago" data-bs-toggle="modal" data-bs-target="#modalPago">Agregar</a></li>
                <li><a href="#" id="btnVerPago" data-bs-toggle="modal" data-bs-target="#modalPagoVer">Ver</a></li>
              </ul>
            </li>

            <li class="dropdown"><a href="#"><span>Tratamientos</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#" id="btnNuevoTratamiento" data-bs-toggle="modal" data-bs-target="#modalTratamiento">Agregar</a></li>
                <li><a href="#" id="btnVerTratamiento" data-bs-toggle="modal" data-bs-target="#modalTratamientoVer">Ver</a></li>
              </ul>
            </li>

            <li><a href="#contact">Contacto</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="cta-btn" href="#appointment">Agendar Cita</a>
      </div>
    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-item active">
          <img src="<?php echo APP_URL; ?>app/views/assets/img/hero-carousel/hero-carousel-1.jpg" alt="">
          <div class="container">
            <h2>Bienvenido a Medicio</h2>
            <p>Ofrecemos atención odontológica moderna, profesional y dedicada al bienestar de tu sonrisa.</p>
            <a href="#about" class="btn-get-started">Leer más</a>
          </div>
        </div>

        <div class="carousel-item">
          <img src="<?php echo APP_URL; ?>app/views/assets/img/hero-carousel/hero-carousel-2.jpg" alt="">
          <div class="container">
            <h2>Atención profesional confiable</h2>
            <p>Brindamos servicios especializados con tecnología avanzada para garantizar diagnósticos precisos.</p>
            <a href="#about" class="btn-get-started">Leer más</a>
          </div>
        </div>

        <div class="carousel-item">
          <img src="<?php echo APP_URL; ?>app/views/assets/img/hero-carousel/hero-carousel-3.jpg" alt="">
          <div class="container">
            <h2>Sonrisas sanas y duraderas</h2>
            <p>Cuidamos tu salud bucal con un enfoque humano, seguro y personalizado.</p>
            <a href="#about" class="btn-get-started">Leer más</a>
          </div>
        </div>

        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left"></span>
        </a>

        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right"></span>
        </a>

        <ol class="carousel-indicators"></ol>

      </div>
    </section>

    <!-- Featured Services -->
    <section id="featured-services" class="featured-services section">
      <div class="container">
        <div class="row gy-4">

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up">
            <div class="service-item position-relative">
              <div class="icon"><i class="fas fa-heartbeat icon"></i></div>
              <h4><a href="" class="stretched-link">Atención Inmediata</a></h4>
              <p>Tratamientos rápidos y seguros para cuidar tu salud bucal.</p>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon"><i class="fas fa-pills icon"></i></div>
              <h4><a href="" class="stretched-link">Cuidado Especializado</a></h4>
              <p>Soluciones profesionales para cada necesidad dental.</p>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon"><i class="fas fa-thermometer icon"></i></div>
              <h4><a href="" class="stretched-link">Diagnóstico Seguro</a></h4>
              <p>Evaluaciones precisas mediante tecnología moderna.</p>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item position-relative">
              <div class="icon"><i class="fas fa-dna icon"></i></div>
              <h4><a href="" class="stretched-link">Tratamientos Avanzados</a></h4>
              <p>Soluciones efectivas diseñadas para tu bienestar.</p>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Call To Action -->
    <section id="call-to-action" class="call-to-action section accent-background">
      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in">
          <div class="col-xl-10">
            <div class="text-center">
              <h3>¿Emergencia? ¿Necesitas ayuda inmediata?</h3>
              <p>Estamos preparados para atenderte con rapidez y profesionalismo ante cualquier urgencia dental.</p>
              <a class="cta-btn" href="#appointment">Agendar Cita</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Sobre Nosotros</h2>
        <p>Dedicados a cuidar tu salud bucal con tecnología moderna y un enfoque humano.</p>
      </div>

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/about.jpg" class="img-fluid" alt="">
            <a href="<?php echo APP_URL; ?>app/views/assets/img/video.mp4" class="glightbox pulsating-play-btn"></a>
          </div>

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Cuidado dental profesional para sonrisas sanas y duraderas.</h3>

            <p class="fst-italic">
              En nuestra clínica ofrecemos tratamientos de alta calidad para todas las edades,
              con un equipo capacitado en brindar atención cercana y personalizada.
            </p>

            <ul>
              <li><i class="bi bi-check2-all"></i> Limpiezas, resinas, extracciones y tratamientos generales.</li>
              <li><i class="bi bi-check2-all"></i> Radiografías digitales para diagnósticos precisos.</li>
              <li><i class="bi bi-check2-all"></i> Especialistas en ortodoncia, endodoncia y estética dental.</li>
            </ul>

            <p>
              Nuestro compromiso es ofrecerte una atención integral en un ambiente cómodo y profesional.
              Queremos que cada visita sea una experiencia positiva para tu salud bucal.
            </p>
          </div>
        </div>

      </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats section">
      <div class="container" data-aos="fade-up">
        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center">
              <i class="fas fa-user-md"></i>
              <div>
                <span data-purecounter-end="25" class="purecounter"></span>
                <p>Doctores</p>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center">
              <i class="far fa-hospital"></i>
              <div>
                <span data-purecounter-end="15" class="purecounter"></span>
                <p>Departamentos</p>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center">
              <i class="fas fa-flask"></i>
              <div>
                <span data-purecounter-end="8" class="purecounter"></span>
                <p>Laboratorios</p>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center">
              <i class="fas fa-award"></i>
              <div>
                <span data-purecounter-end="150" class="purecounter"></span>
                <p>Premios</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
<!-- Features Section -->
<section id="features" class="features section">

  <div class="container">

    <div class="row justify-content-around gy-4">
      <div class="features-image col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <img src="<?php echo APP_URL; ?>app/views/assets/img/features.jpg" alt="">
      </div>

      <div class="col-lg-5 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
        <h3>Tratamientos confiables y de alta calidad para tu bienestar</h3>
        <p>Ofrecemos atención moderna, segura y profesional mediante procedimientos diseñados para mejorar tu salud bucal.</p>

        <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="300">
          <i class="fa-solid fa-hand-holding-medical flex-shrink-0"></i>
          <div>
            <h4><a href="" class="stretched-link">Atención Personalizada</a></h4>
            <p>Brindamos una experiencia cómoda y enfocada en tus necesidades.</p>
          </div>
        </div>

        <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="400">
          <i class="fa-solid fa-suitcase-medical flex-shrink-0"></i>
          <div>
            <h4><a href="" class="stretched-link">Diagnóstico Preciso</a></h4>
            <p>Utilizamos herramientas digitales para asegurar resultados confiables.</p>
          </div>
        </div>

        <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="500">
          <i class="fa-solid fa-staff-snake flex-shrink-0"></i>
          <div>
            <h4><a href="" class="stretched-link">Tratamientos Especializados</a></h4>
            <p>Expertos en ortodoncia, endodoncia, estética y más.</p>
          </div>
        </div>

        <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="600">
          <i class="fa-solid fa-lungs flex-shrink-0"></i>
          <div>
            <h4><a href="" class="stretched-link">Cuidados Integrales</a></h4>
            <p>Cubrimos todas las áreas para mantener tu sonrisa saludable.</p>
          </div>
        </div>

      </div>
    </div>

  </div>

</section>


<!-- Services Section -->
<section id="services" class="services section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Servicios</h2>
    <p>Soluciones profesionales diseñadas para el cuidado completo de tu salud bucal.</p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-heartbeat"></i></div>
          <a href="#" class="stretched-link">
            <h3>Evaluación Dental</h3>
          </a>
          <p>Revisión completa para detectar problemas y planificar tu tratamiento.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-pills"></i></div>
          <a href="#" class="stretched-link">
            <h3>Procedimientos Dentales</h3>
          </a>
          <p>Resinas, endodoncias, extracciones y más realizados por especialistas.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-hospital-user"></i></div>
          <a href="#" class="stretched-link">
            <h3>Cuidados Preventivos</h3>
          </a>
          <p>Evita problemas futuros con limpiezas y mantenimiento profesional.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-dna"></i></div>
          <a href="#" class="stretched-link">
            <h3>Ortodoncia</h3>
          </a>
          <p>Corrección de alineación dental y mejora estética de la sonrisa.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-wheelchair"></i></div>
          <a href="#" class="stretched-link">
            <h3>Rehabilitación Oral</h3>
          </a>
          <p>Restauración completa mediante prótesis, coronas y puentes.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
        <div class="service-item position-relative">
          <div class="icon"><i class="fas fa-notes-medical"></i></div>
          <a href="#" class="stretched-link">
            <h3>Estética Dental</h3>
          </a>
          <p>Blanqueamientos, carillas y mejoras estéticas personalizadas.</p>
        </div>
      </div>

    </div>
  </div>

</section>


<!-- Appointment Section -->
<section id="appointment" class="appointment section light-background">

  <div class="container section-title" data-aos="fade-up">
    <h2>AGENDAR UNA CITA</h2>
    <p>Completa el formulario y pronto nos pondremos en contacto contigo.</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <form action="forms/appointment.php" method="post" role="form" class="php-email-form">
      <div class="row">
        <div class="col-md-4 form-group">
          <input type="text" name="name" class="form-control" placeholder="Tu Nombre" required>
        </div>
        <div class="col-md-4 form-group mt-3 mt-md-0">
          <input type="email" name="email" class="form-control" placeholder="Tu Correo" required>
        </div>
        <div class="col-md-4 form-group mt-3 mt-md-0">
          <input type="tel" name="phone" class="form-control" placeholder="Tu Teléfono" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 form-group mt-3">
          <input type="datetime-local" name="date" class="form-control" required>
        </div>

        <div class="col-md-4 form-group mt-3">
          <select name="department" class="form-select" required>
            <option value="">Seleccionar Departamento</option>
            <option value="Departamento 1">Departamento 1</option>
            <option value="Departamento 2">Departamento 2</option>
            <option value="Departamento 3">Departamento 3</option>
          </select>
        </div>

        <div class="col-md-4 form-group mt-3">
          <select name="doctor" class="form-select" required>
            <option value="">Seleccionar Doctor</option>
            <option value="Doctor 1">Doctor 1</option>
            <option value="Doctor 2">Doctor 2</option>
            <option value="Doctor 3">Doctor 3</option>
          </select>
        </div>
      </div>

      <div class="form-group mt-3">
        <textarea class="form-control" name="message" rows="5" placeholder="Mensaje (Opcional)"></textarea>
      </div>

      <div class="mt-3">
        <div class="loading">Cargando</div>
        <div class="error-message"></div>
        <div class="sent-message">Tu solicitud ha sido enviada exitosamente. ¡Gracias!</div>
        <div class="text-center"><button type="submit">Agendar Cita</button></div>
      </div>
    </form>

  </div>

</section>


<!-- Tabs Section (Departments) -->
<section id="tabs" class="tabs section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Departamentos</h2>
    <p>Descubre las áreas especializadas con las que contamos.</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row">
      <div class="col-lg-3">
        <ul class="nav nav-tabs flex-column">

          <li class="nav-item">
            <a class="nav-link active show" data-bs-toggle="tab" href="#tabs-tab-1">Cardiología</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabs-tab-2">Neurología</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabs-tab-3">Hepatología</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabs-tab-4">Pediatría</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tabs-tab-5">Oftalmología</a>
          </li>

        </ul>
      </div>

      <div class="col-lg-9 mt-4 mt-lg-0">
        <div class="tab-content">

          <!-- Tab 1 -->
          <div class="tab-pane active show" id="tabs-tab-1">
            <div class="row">
              <div class="col-lg-8 details order-2 order-lg-1">
                <h3>Cardiología</h3>
                <p class="fst-italic">Atención especializada en el cuidado del corazón y sistema circulatorio.</p>
                <p>Realizamos estudios y diagnósticos completos para detectar cualquier anomalía cardiovascular de forma temprana.</p>
              </div>
              <div class="col-lg-4 text-center order-1 order-lg-2">
                <img src="<?php echo APP_URL; ?>app/views/assets/img/departments-1.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- Tab 2 -->
          <div class="tab-pane" id="tabs-tab-2">
            <div class="row">
              <div class="col-lg-8 details order-2 order-lg-1">
                <h3>Neurología</h3>
                <p class="fst-italic">Diagnóstico y tratamiento de trastornos del sistema nervioso.</p>
                <p>Atendemos migrañas, epilepsia, neuropatías y más, con estudios especializados y alta precisión médica.</p>
              </div>
              <div class="col-lg-4 text-center order-1 order-lg-2">
                <img src="<?php echo APP_URL; ?>app/views/assets/img/departments-2.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- Tab 3 -->
          <div class="tab-pane" id="tabs-tab-3">
            <div class="row">
              <div class="col-lg-8 details order-2 order-lg-1">
                <h3>Hepatología</h3>
                <p class="fst-italic">Cuidado especializado del hígado y sistema digestivo.</p>
                <p>Evaluamos enfermedades hepáticas, digestivas y metabólicas con métodos modernos y efectivos.</p>
              </div>
              <div class="col-lg-4 text-center order-1 order-lg-2">
                <img src="<?php echo APP_URL; ?>app/views/assets/img/departments-3.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- Tab 4 -->
          <div class="tab-pane" id="tabs-tab-4">
            <div class="row">
              <div class="col-lg-8 details order-2 order-lg-1">
                <h3>Pediatría</h3>
                <p class="fst-italic">Atención especializada para niños de todas las edades.</p>
                <p>Brindamos cuidados preventivos y tratamientos para garantizar un desarrollo saludable.</p>
              </div>
              <div class="col-lg-4 text-center order-1 order-lg-2">
                <img src="<?php echo APP_URL; ?>app/views/assets/img/departments-4.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- Tab 5 -->
          <div class="tab-pane" id="tabs-tab-5">
            <div class="row">
              <div class="col-lg-8 details order-2 order-lg-1">
                <h3>Oftalmología</h3>
                <p class="fst-italic">Cuidado de la visión y salud ocular.</p>
                <p>Realizamos diagnósticos, evaluaciones visuales y tratamientos completos para diversas condiciones oculares.</p>
              </div>
              <div class="col-lg-4 text-center order-1 order-lg-2">
                <img src="<?php echo APP_URL; ?>app/views/assets/img/departments-5.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>

</section>


<!-- Doctors Section -->
<section id="doctors" class="doctors section light-background">

  <div class="container section-title" data-aos="fade-up">
    <h2>Doctores</h2>
    <p>Conoce a nuestros profesionales altamente calificados.</p>
  </div>

  <div class="container">

    <div class="row gy-4">

      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
        <div class="team-member">
          <div class="member-img">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/doctors/doctors-1.jpg" class="img-fluid" alt="">
            <div class="social">
              <a href=""><i class="bi bi-twitter-x"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
          <div class="member-info">
            <h4>Walter White</h4>
            <span>Director Médico</span>
          </div>
        </div>
      </div>


      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
        <div class="team-member">
          <div class="member-img">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/doctors/doctors-2.jpg" class="img-fluid" alt="">
            <div class="social">
              <a href=""><i class="bi bi-twitter-x"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
          <div class="member-info">
            <h4>Sarah Jhonson</h4>
            <span>Anestesióloga</span>
          </div>
        </div>
      </div>


      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
        <div class="team-member">
          <div class="member-img">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/doctors/doctors-3.jpg" class="img-fluid" alt="">
            <div class="social">
              <a href=""><i class="bi bi-twitter-x"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
          <div class="member-info">
            <h4>William Anderson</h4>
            <span>Cardiólogo</span>
          </div>
        </div>
      </div>


      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
        <div class="team-member">
          <div class="member-img">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/doctors/doctors-4.jpg" class="img-fluid" alt="">
            <div class="social">
              <a href=""><i class="bi bi-twitter-x"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
          <div class="member-info">
            <h4>Amanda Jepson</h4>
            <span>Neurocirujana</span>
          </div>
        </div>
      </div>

    </div>

  </div>

</section>


<!-- Gallery Section -->
<section id="gallery" class="gallery section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Galería</h2>
    <p>Conoce más de nuestras instalaciones y equipo de trabajo.</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="swiper init-swiper">

      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": { "delay": 5000 },
          "slidesPerView": "auto",
          "centeredSlides": true,
          "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
          "breakpoints": {
            "320": { "slidesPerView": 1, "spaceBetween": 0 },
            "768": { "slidesPerView": 3, "spaceBetween": 20 },
            "1200": { "slidesPerView": 5, "spaceBetween": 20 }
          }
        }
      </script>

      <div class="swiper-wrapper align-items-center">
        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-1.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-1.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-2.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-2.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-3.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-3.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-4.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-4.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-5.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-5.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-6.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-6.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-7.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-7.jpg" class="img-fluid" alt="">
          </a>
        </div>

        <div class="swiper-slide">
          <a class="glightbox" data-gallery="images-gallery" href="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-8.jpg">
            <img src="<?php echo APP_URL; ?>app/views/assets/img/gallery/gallery-8.jpg" class="img-fluid" alt="">
          </a>
        </div>

      </div>

      <div class="swiper-pagination"></div>

    </div>

  </div>

</section>


<!-- FAQ Section -->
<section id="faq" class="faq section light-background">

  <div class="container section-title" data-aos="fade-up">
    <h2>Preguntas Frecuentes</h2>
    <p>Encuentra respuestas a las dudas más comunes.</p>
  </div>

  <div class="container">

    <div class="row justify-content-center">
      <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

        <div class="faq-container">

          <div class="faq-item">
            <h3>¿Cómo puedo agendar una cita?</h3>
            <div class="faq-content">
              <p>Puedes hacerlo desde nuestro formulario en línea o llamándonos directamente.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

          <div class="faq-item">
            <h3>¿Aceptan emergencias?</h3>
            <div class="faq-content">
              <p>Sí, contamos con atención inmediata para emergencias dentales.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

          <div class="faq-item">
            <h3>¿Realizan tratamientos para niños?</h3>
            <div class="faq-content">
              <p>Sí, nuestros especialistas en odontopediatría brindan atención segura y amigable para los más pequeños.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

          <div class="faq-item">
            <h3>¿Trabajan con seguros médicos?</h3>
            <div class="faq-content">
              <p>Contáctanos para verificar la disponibilidad con tu proveedor de seguros.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

          <div class="faq-item">
            <h3>¿Puedo financiar mis tratamientos?</h3>
            <div class="faq-content">
              <p>Ofrecemos planes de pago accesibles según el tratamiento requerido.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

          <div class="faq-item">
            <h3>¿Cómo sé qué tratamiento necesito?</h3>
            <div class="faq-content">
              <p>Nuestros especialistas realizan un diagnóstico completo para definir el tratamiento adecuado.</p>
            </div>
            <i class="faq-toggle bi bi-chevron-right"></i>
          </div>

        </div>

      </div>
    </div>

  </div>

</section>
<!-- Contact Section -->
<section id="contact" class="contact section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Contacto</h2>
    <p>Ponte en contacto con nosotros para consultas, citas o información adicional.</p>
  </div>

  <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
    <iframe style="border:0; width: 100%; height: 370px;"
      src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus"
      frameborder="0" allowfullscreen="" loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row gy-4">
      <div class="col-lg-6 ">
        <div class="row gy-4">

          <div class="col-lg-12">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Dirección</h3>
              <p>A108 Adam Street, New York, NY 535022</p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Teléfono</h3>
              <p>+1 5589 55488 55</p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Correo Electrónico</h3>
              <p>info@example.com</p>
            </div>
          </div>

        </div>
      </div>

      <div class="col-lg-6">
        <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="500">
          <div class="row gy-4">

            <div class="col-md-6">
              <input type="text" name="name" class="form-control" placeholder="Tu Nombre" required="">
            </div>

            <div class="col-md-6">
              <input type="email" class="form-control" name="email" placeholder="Tu Correo" required="">
            </div>

            <div class="col-md-12">
              <input type="text" class="form-control" name="subject" placeholder="Asunto" required="">
            </div>

            <div class="col-md-12">
              <textarea class="form-control" name="message" rows="4" placeholder="Mensaje" required=""></textarea>
            </div>

            <div class="col-md-12 text-center">
              <div class="loading">Cargando</div>
              <div class="error-message"></div>
              <div class="sent-message">Tu mensaje ha sido enviado. ¡Gracias!</div>
              <button type="submit">Enviar Mensaje</button>
            </div>

          </div>
        </form>
      </div>

    </div>

  </div>

</section>


<!-- Footer -->
<footer id="footer" class="footer light-background">

  <div class="container footer-top">
    <div class="row gy-4">

      <div class="col-lg-4 col-md-6 footer-about">
        <a href="index.html" class="logo d-flex align-items-center">
          <span class="sitename">Medicio</span>
        </a>
        <div class="footer-contact pt-3">
          <p>A108 Adam Street</p>
          <p>New York, NY 535022</p>
          <p class="mt-3"><strong>Teléfono:</strong> <span>+1 5589 55488 55</span></p>
          <p><strong>Correo:</strong> <span>info@example.com</span></p>
        </div>
        <div class="social-links d-flex mt-4">
          <a href=""><i class="bi bi-twitter-x"></i></a>
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Enlaces Útiles</h4>
        <ul>
          <li><a href="#">Inicio</a></li>
          <li><a href="#">Sobre Nosotros</a></li>
          <li><a href="#">Servicios</a></li>
          <li><a href="#">Términos de Servicio</a></li>
          <li><a href="#">Política de Privacidad</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Nuestros Servicios</h4>
        <ul>
          <li><a href="#">Diseño Web</a></li>
          <li><a href="#">Desarrollo Web</a></li>
          <li><a href="#">Gestión de Productos</a></li>
          <li><a href="#">Marketing</a></li>
          <li><a href="#">Diseño Gráfico</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Información</h4>
        <ul>
          <li><a href="#">Consultas Generales</a></li>
          <li><a href="#">Preguntas Frecuentes</a></li>
          <li><a href="#">Soporte Técnico</a></li>
          <li><a href="#">Atención al Cliente</a></li>
          <li><a href="#">Documentación</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Recursos</h4>
        <ul>
          <li><a href="#">Guías</a></li>
          <li><a href="#">Tutoriales</a></li>
          <li><a href="#">Noticias</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">Foros</a></li>
        </ul>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p>© <strong class="px-1 sitename">Medicio</strong> Todos los Derechos Reservados</p>
    <div class="credits">
      Diseñado por <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </div>

</footer>


<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/php-email-form/validate.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/aos/aos.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Main Template JS -->
<script src="<?php echo APP_URL; ?>app/views/assets/js/main.js"></script>

<!-- Custom JS -->
<script src="<?php echo APP_URL; ?>app/ajax/paciente.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/odontologo.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/cita.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/odontograma.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/servicio.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/disponibilidad.js"></script>
<script src="<?php echo APP_URL; ?>app/ajax/tratamiento.js"></script>
<!--<script src="<?php echo APP_URL; ?>app/ajax/pago.js"></script>-->



<?php include 'app/views/content/paciente.php'; ?>
<?php include 'app/views/content/ver_paciente.php'; ?>
<?php include 'app/views/content/odontologo.php'; ?>
<?php include 'app/views/content/ver_odontologo.php'; ?>
<?php include 'app/views/content/cita.php'; ?>
<?php include 'app/views/content/odontograma.php'; ?>
<?php include 'app/views/content/servicio.php'; ?>
<?php include 'app/views/content/disponibilidad.php'; ?>
<?php include 'app/views/content/tratamiento.php'; ?>
<?php include 'app/views/content/pago.php'; ?>

</body>
</html>
