<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);

if (!isset($usuario)) {
    $usuario = [
        'nombre' => 'Usuario invitado',
        'bio' => '',
        'foto' => 'web/img/perfil/default.png'
    ];
}

if (!isset($topLibros)) $topLibros = [];
if (!isset($topPeliculas)) $topPeliculas = [];
if (!isset($listas)) $listas = [];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil</title>

    <link rel="stylesheet" href="web/css/stylePerfil.css">
    <link rel="stylesheet" href="web/css/styleFuentes.css">
</head>

<body>

<?php include_once __DIR__ . '/../templates/header.php'; ?>

<main class="perfil-page">

    <!-- PERFIL -->
    <section class="perfil-header">
        <?php
        $fotoPerfil = trim($usuario['foto'] ?? '');
        if ($fotoPerfil === '' || !file_exists($fotoPerfil)) {
            $fotoPerfil = 'web/img/perfil/default.png';
        }
        ?>

        <div class="perfil-container">

            <div class="perfil-avatar">
                <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
            </div>

            <div class="perfil-info">

                <h2><?= htmlspecialchars($usuario['username']) ?></h2>
                <p class="bio"><?= htmlspecialchars($usuario['bio'] ?? '') ?></p>

                <div class="perfil-botones">

                    <!-- AJUSTES SOLO SI ES TU PERFIL -->
                    <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                        <a href="index.php?ctl=ajustesPerfil" class="btn-outline">Ajustes</a>
                    <?php endif; ?>

                    <!-- PANEL ADMIN (solo si eres admin y es tu perfil) -->
                    <?php if (isset($_SESSION['usuarioNivel']) && $_SESSION['usuarioNivel'] == 3 && $_SESSION['usuarioId'] == $usuario['id']): ?>
                        <a href="index.php?ctl=panelAdmin" class="btn-warning">Panel Admin</a>
                    <?php endif; ?>

                    <!-- BOTÓN SEGUIR / DEJAR DE SEGUIR -->
                    <?php if ($_SESSION['id_usuario'] !== $usuario['id']): ?>
                        <?php if ($esSeguidor): ?>
                            <a href="index.php?ctl=dejarseguir&id=<?= $usuario['id'] ?>" class="btn-outline-danger">Dejar de seguir</a>
                        <?php else: ?>
                            <a href="index.php?ctl=seguir&id=<?= $usuario['id'] ?>" class="btn-primary">Seguir</a>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <div class="perfil-stats-wrapper">

                    <div class="stats-izquierda">
                        <div>
                            <strong>32</strong>
                            <span>Libros</span>
                        </div>

                        <div>
                            <strong><?= $numeroListas ?? 0 ?></strong>
                            <span>Listas</span>
                        </div>

                        <div>
                            <strong>14</strong>
                            <span>Reseñas</span>
                        </div>
                    </div>

                    <div class="stats-derecha">
                        <a href="index.php?ctl=verSeguidores&id=<?= $usuario['id'] ?>">
                            <strong><?= count($seguidores) ?></strong>
                            <span>Seguidores</span>
                        </a>

                        <a href="index.php?ctl=verSeguidos&id=<?= $usuario['id'] ?>">
                            <strong><?= count($seguidos) ?></strong>
                            <span>Siguiendo</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- CONTENIDO -->
    <section class="perfil-contenido">

        <div class="bloques-superiores">

            <!-- TOP LIBROS -->
            <div class="card">
                <h3>Top Libros</h3>
                <div class="top-grid">
                    <?php foreach ($topLibros as $libro): ?>
                        <div class="top-item">
                            <img src="<?= $libro['imagen_url'] ?>">
                            <p><?= htmlspecialchars($libro['titulo']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- TOP PELÍCULAS -->
            <div class="card">
                <h3>Top Películas</h3>
                <div class="top-grid">
                    <?php foreach ($topPeliculas as $peli): ?>
                        <div class="top-item">
                            <img src="<?= $peli['portada'] ?? 'web/img/fallback.png' ?>">
                            <p><?= htmlspecialchars($peli['titulo'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- LISTAS -->
        <div class="card listas">
            <h3>Mis listas (<?= $numeroListas ?? 0 ?>)</h3>

            <div class="listas-container">
                <?php foreach ($listas as $lista): ?>
                    <div class="lista-item-card">
                        <div class="lista-texto">
                            <strong><?= htmlspecialchars($lista['nombre']) ?></strong>
                            <p><?= htmlspecialchars($lista['descripcion']) ?></p>
                        </div>

                        <div class="d-flex justify-content-end gap-2">

                            <!-- Ver lista SIEMPRE -->
                            <a href="index.php?ctl=verLista&id=<?= $lista['id'] ?>" class="btn-success">
                                Ver lista
                            </a>

                            <!-- Eliminar SOLO si es tu perfil -->
                            <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                                <a href="index.php?ctl=eliminarLista&id=<?= $lista['id'] ?>" class="btn btn-danger rounded-5 ps-3 pe-3 fw-semibold">
                                    Eliminar
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Crear lista SOLO si es tu perfil -->
            <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                <a href="index.php?ctl=crearLista" class="btn-success crear-lista">
                    Crear nueva lista
                </a>
            <?php endif; ?>

        </div>

    </section>

</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>

</body>
</html>
