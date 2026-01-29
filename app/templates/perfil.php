<?php
// Fallback para evitar errores si alguien abre la vista directamente
if (!isset($usuario)) {
    $usuario = [
        'nombre' => 'Usuario invitado',
        'bio' => '',
        'foto' => '/INDEX_proyecto/web/img/default.jpg'
    ];
}

if (!isset($topLibros)) $topLibros = [];
if (!isset($topPeliculas)) $topPeliculas = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INDEX – Perfil</title>
<<<<<<< HEAD
<link rel="stylesheet" href="/Proyecto/web/css/stylePerfil.css">
=======
    <link rel="stylesheet" href="/INDEX_proyecto/web/css/styleperfil.css">
>>>>>>> 778022a518ea1c570315cc2e7a8c8f58e0d462c6
</head>
<body>

<header>
    <nav>
        <h1>INDEX</h1>
        <ul>
            <li><a href="index.php?controller=usuario&action=perfil">Perfil</a></li>
            <li><a href="index.php?controller=peliculas&action=cargarPeliculas">Cargar Películas</a></li>
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<main>

    <!-- PERFIL -->
    <section id="perfil">
        <img src="<?= $usuario['foto'] ?>" alt="Foto de perfil">
        <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
        <p><?= htmlspecialchars($usuario['bio']) ?></p>

        <div id="estadisticas">
            <div class="stat"><strong>32</strong><span>Libros leídos</span></div>
            <div class="stat"><strong>8</strong><span>Listas creadas</span></div>
            <div class="stat"><strong>14</strong><span>Reseñas</span></div>
        </div>
    </section>

    <hr>

    <!-- TOP 5 LIBROS + TOP 5 PELÍCULAS -->
    <div class="top-container">

        <!-- COLUMNA IZQUIERDA: TOP 5 LIBROS -->
        <div class="top-col">
            <h2>📚 Top 5 Libros</h2>

            <?php foreach ($topLibros as $libro): ?>
                <div class="top-item">
                    <img src="<?= $libro['imagen_url'] ?>" alt="<?= $libro['titulo'] ?>">
                    <div class="top-item-info">
                        <strong><?= $libro['titulo'] ?></strong>
                        <small><?= $libro['autores'] ?></small>
                        <small><?= $libro['categoria'] ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- COLUMNA DERECHA: TOP 5 PELÍCULAS -->
        <div class="top-col">
            <h2>🎬 Top 5 Películas</h2>

            <?php foreach ($topPeliculas as $peli): ?>
                <div class="top-item">
                    <img src="<?= $peli['portada'] ?>" alt="<?= $peli['titulo'] ?>">
                    <div class="top-item-info">
                        <strong><?= $peli['titulo'] ?></strong>
                        <small><?= obtenerNombreGenero($peli['genero']) ?></small>
                        <small><?= $peli['anio'] ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</main>

</body>
</html>
