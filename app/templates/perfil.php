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
    <title>INDEX – Perfil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="web/css/styleperfil.css">
</head>

<body>

    <?php include_once __DIR__ . '/../templates/header.php'; ?>
<main>

<section id="perfil">

<?php
$fotoPerfil = $usuario['foto'] ?? '';
$fotoPerfil = trim($fotoPerfil);

if ($fotoPerfil === '' || !file_exists($fotoPerfil)) {
    $fotoPerfil = 'web/img/perfil/default.png';
}
?>

<img class="foto-perfil" src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">

<h2><?= htmlspecialchars($usuario['username']) ?></h2>
<p><?= htmlspecialchars($usuario['bio'] ?? '') ?></p>



            <a href="index.php?ctl=ajustesPerfil" class="btn btn-outline-primary mt-3">
                Ajustes del perfil
            </a>
            <?php if ($idUsuario !== $usuario['id']): ?>
                <?php if ($esSeguidor): ?>
                    <a href="index.php?ctl=dejarseguir&id=<?= $usuario['id'] ?>"
                        class="btn btn-outline-danger mt-3">
                        Dejar de seguir
                    </a>
                <?php else: ?>
                    <a href="index.php?ctl=seguir&id=<?= $usuario['id'] ?>"
                        class="btn btn-primary mt-3">
                        Seguir
                    </a>
                <?php endif; ?>
            <?php endif; ?>




            <div id="estadisticas">
                <div class="stat"><strong>32</strong><span>Libros leídos</span></div>
                <div class="stat"><strong><?= $numeroListas ?? 0 ?></strong><span>Listas</span></div>
                <div class="stat"><strong>14</strong><span>Reseñas</span></div>
            </div>

            <div class="stat">
                <a href="index.php?ctl=verSeguidores&id=<?= $usuario['id'] ?>" class="text-decoration-none">
                    <strong><?= count($seguidores) ?></strong>
                    <span>Seguidores</span>
                </a>
            </div>

            <div class="stat">
                <a href="index.php?ctl=verSeguidos&id=<?= $usuario['id'] ?>" class="text-decoration-none">
                    <strong><?= count($seguidos) ?></strong>
                    <span>Siguiendo</span>
                </a>
            </div>


        </section>

        <hr>

        <div class="bootstrap-zone">
            <div class="container-fluid mt-5">

                <div class="row g-3 mb-4">

                    <!-- TOP LIBROS -->
                    <div class="col-lg-4">
                        <div class="p-3 border rounded bg-light h-100">
                            <h4 class="text-center mb-3">📚 Top 4 Libros</h4>
                            <div class="row g-2">
                                <?php foreach ($topLibros as $libro): ?>
                                    <div class="col-6 col-md-3">
                                        <img src="<?= $libro['imagen_url'] ?>" class="img-fluid rounded">
                                        <p><?= htmlspecialchars($libro['titulo']) ?></p>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>

                    <!-- TOP PELÍCULAS -->
                    <div class="col-lg-4">
                        <div class="p-3 border rounded bg-light h-100">
                            <h4 class="text-center mb-3">🎬 Top 4 Películas</h4>
                            <div class="row g-2">
                                <?php foreach ($topPeliculas as $peli): ?>
                                    <div class="col-6 col-md-6">
                                        <div class="top-item">
                                            <img src="<?= $peli['portada'] ?? 'web/img/fallback.png' ?>" alt="<?= htmlspecialchars($peli['titulo'] ?? 'Película') ?>">
                                            <strong><?= htmlspecialchars($peli['titulo'] ?? 'Sin título') ?></strong>
                                            <small><?= htmlspecialchars($peli['genero_nombre'] ?? 'Desconocido') ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- MIS LISTAS -->
                    <div class="col-lg-4">
                        <div class="p-4 border rounded bg-light h-100 shadow-sm">
                            <h2>Mis listas (<?= $numeroListas ?? 0 ?>)</h2>

                            <?php if (($numeroListas ?? 0) === 0): ?>
                                <p>No tienes listas creadas todavía.</p>
                            <?php else: ?>
                                <ul>
                                    <?php foreach ($listas as $lista): ?>
                                        <li>
                                            <strong><?= htmlspecialchars($lista['nombre'] ?? 'Sin nombre') ?></strong><br>
                                            <em><?= htmlspecialchars($lista['tipo'] ?? 'Desconocido') ?></em><br>
                                            <?= htmlspecialchars($lista['descripcion'] ?? 'Sin descripción') ?><br>
                                            <small>Creada el: <?= htmlspecialchars($lista['creada_en'] ?? 'N/A') ?></small><br>
                                            <!-- Enlace solo para ver la lista -->
                                            <a href="index.php?ctl=verLista&id=<?= $lista['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                                Ver lista
                                            </a>

                                        </li>
                                        <hr>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <a href="index.php?ctl=crearLista" class="btn btn-success w-100 mt-3">
                                Crear nueva lista
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </main>

    <?php include_once __DIR__ . '/../templates/footer.php'; ?>
</body>

</html>