
<?php

// Cargar controladores
require_once __DIR__.'/app/Controllers/UsuarioController.php';
require_once __DIR__.'/app/Controllers/PeliculasController.php';

// Obtener parámetros de la URL
$controllerName = $_GET['controller'] ?? 'usuario';
$action = $_GET['action'] ?? 'perfil';

// Normalizar nombre del controlador
$controllerName = strtolower($controllerName);

// Crear instancia del controlador correspondiente
switch ($controllerName) {
    case 'usuario':
        $controller = new UsuarioController();
        break;

    case 'peliculas':
        $controller = new PeliculasController();
        break;

    default:
        die("Controlador no encontrado.");
}

// Verificar que la acción existe
if (!method_exists($controller, $action)) {
    die("Acción '$action' no encontrada en el controlador '$controllerName'.");
}

// Ejecutar acción
$controller->$action();

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INDEX – Timeline</title>
    <link rel="stylesheet" href="../../web/css/styleTimeline.css">
</head>
<body>

<header>
    <nav>
        <h1>Actividad</h1>
        <ul>
            <li><a href="index.php?controller=usuario&action=perfil">Perfil</a></li>
            <li><a href="">Listas</a></li>
            <li><a href="">Salir</a></li>
        </ul>
    </nav>
</header>

<main>

<section id="timeline">
  <h2>Actividad reciente</h2>

  <ul class="timeline-list">

    <!-- Creación de listas -->
    <li class="timeline-item evento-lista">
      <span class="timestamp">27/01/2025</span>
      <div class="content">
        <h3>Isabel creó la lista "Pendientes"</h3>
        <p>La lista ya está disponible en su perfil.</p>
      </div>
    </li>

    <!-- Añadir libro/película a una lista -->
    <li class="timeline-item evento-lista">
      <span class="timestamp">27/01/2025</span>
      <div class="content">
        <h3>Miguel añadió "El Principito" a Favoritos</h3>
        <p>Libro añadido correctamente.</p>
      </div>
    </li>

    <!-- Marcar como leído/visto -->
    <li class="timeline-item evento-lectura">
      <span class="timestamp">26/01/2025</span>
      <div class="content">
        <h3>Isabel marcó "1984" como leído</h3>
        <p>Se actualizó su progreso de lectura.</p>
      </div>
    </li>

    <!-- Valoración -->
    <li class="timeline-item evento-valoracion">
      <span class="timestamp">25/01/2025</span>
      <div class="content">
        <h3>Miguel valoró "Interstellar"</h3>
        <p>Puntuación: ★★★★★</p>
      </div>
    </li>

    <!-- Nuevo usuario -->
    <li class="timeline-item evento-usuario">
      <span class="timestamp">24/01/2025</span>
      <div class="content">
        <h3>Nuevo usuario registrado</h3>
        <p>Laura se ha unido a la plataforma.</p>
      </div>
    </li>

    <!-- Cambios en el perfil -->
    <li class="timeline-item evento-usuario">
      <span class="timestamp">24/01/2025</span>
      <div class="content">
        <h3>Isabel actualizó su foto de perfil</h3>
        <p>La imagen se ha cambiado correctamente.</p>
      </div>
    </li>

    <!-- Acciones del administrador -->
    <li class="timeline-item evento-admin">
      <span class="timestamp">23/01/2025</span>
      <div class="content">
        <h3>El administrador eliminó la lista "Ver más tarde"</h3>
        <p>La lista ya no está disponible.</p>
      </div>
    </li>

    <!-- Reseñas -->
    <li class="timeline-item evento-resena">
      <span class="timestamp">22/01/2025</span>
      <div class="content">
        <h3>Isabel escribió una reseña sobre "El Hobbit"</h3>
        <p>“Una aventura increíble con un ritmo perfecto.”</p>
      </div>
    </li>

    <!-- Comentarios -->
    <li class="timeline-item evento-comentario">
      <span class="timestamp">22/01/2025</span>
      <div class="content">
        <h3>Miguel comentó en la ficha de "Blade Runner"</h3>
        <p>“Visualmente espectacular, una obra maestra.”</p>
      </div>
    </li>

  </ul>
</section>

</main>

</body>
</html>
