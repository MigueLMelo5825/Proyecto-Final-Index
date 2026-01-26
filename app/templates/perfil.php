<?php
require_once dirname(__DIR__).'/Core/Database.php';
require_once dirname(__DIR__).'/Models/Libros.php';
require_once dirname(__DIR__).'/Models/Peliculas.php';

// Conexión
$pdo = Database::getConnection();

// Datos
$topLibros = obtenerTopLibros($pdo);
$topPeliculas = obtenerTopPeliculas($pdo);

// Usuario simulado
$usuario = [
    'nombre' => 'Isabel Paredes',
    'bio' => 'De Alfara del Patriarca',
    'foto' => '/INDEX_proyecto/web/img/default.jpg'
];  
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INDEX – Perfil</title>
    <link rel="stylesheet" href="../../web/css/stylePerfil.css">
</head>
<body>

<header>
    <nav>
        <h1>INDEX</h1>
        <input type="text" id="buscador" placeholder="Buscar libros y Peliculas">
        <ul> 
            <li><a href="index.php">Inicio</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="login.php">Salir</a></li>
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

    <!-- LISTAS -->
    <section id="listas">
        <h3>Mis Listas</h3>

        <div class="listas-dos-columnas">

            <!-- LISTAS DE LIBROS -->
            <div class="columna-listas">
                <h4>Listas de Libros</h4>

                <div class="lista-card">
                    <h4>Favoritos</h4>
                    <p>12 libros</p>
                </div>

                <div class="lista-card">
                    <h4>Pendientes</h4>
                    <p>8 libros</p>
                </div>

                <div class="lista-card nueva-lista">
                    <span>+</span>
                    <p>Crear nueva lista</p>
                </div>
            </div>

            <!-- LISTAS DE PELÍCULAS -->
            <div class="columna-listas">
                <h4>Listas de Películas</h4>

                <div class="lista-card">
                    <h4>Favoritas</h4>
                    <p>6 películas</p>
                </div>

                <div class="lista-card">
                    <h4>Pendientes</h4>
                    <p>10 películas</p>
                </div>

                <div class="lista-card nueva-lista">
                    <span>+</span>
                    <p>Crear nueva lista</p>
                </div>
            </div>

        </div>
    </section>

</main>

</body>
</html>
