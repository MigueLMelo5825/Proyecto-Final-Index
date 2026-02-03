<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);

//http://localhost/Proyecto/index.php?ctl=perfil

if (!isset($usuario)) {
    $usuario = [
        'nombre' => 'Usuario invitado',
        'bio' => '',
        'foto' => 'web/img/default.jpg'
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

    <link rel="stylesheet" href="web/css/styleperfil.css">
</head>
<body>

<header>
    <nav>
        <h1>INDEX</h1>
        <div id="buscador">
            <p>Buscar: <input type="text" id="inputLibro"></p>
            <div id="libroOPeliculaEncontrada"></div>
        </div>
        <ul>
            <li><a href="index.php?controller=usuario&action=perfil">Perfil</a></li>
            <li><a href="index.php?controller=peliculas&action=cargarPeliculas">Cargar Películas</a></li>
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>
</header>
=======
    <link rel="stylesheet" href="web/css/styleperfil.css">
</head>
<body>
    
<?php include_once __DIR__.'/../Models/header.php'; ?>
>>>>>>> f66ed70b4c4f00f925fd3f7b57da556279dd7cdd

<main>

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

    <div class="top-container">

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

        <div class="top-col">
            <h2>🎬 Top 5 Películas</h2>

            <?php foreach ($topPeliculas as $peli): ?>
                <div class="top-item">
                    <img src="<?= $peli['portada'] ?>" alt="<?= $peli['titulo'] ?>">
                    <div class="top-item-info">
                        <strong><?= $peli['titulo'] ?></strong>
                        <small><?= $peli['genero_nombre'] ?></small>
                        <small><?= $peli['anio'] ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</main>

<<<<<<< HEAD
<script src="web/js/buscadorLibrosYPeliculas.js"></script>
=======
<?php include_once __DIR__.'/../Models/footer.php'; ?>
>>>>>>> f66ed70b4c4f00f925fd3f7b57da556279dd7cdd
</body>
</html>
