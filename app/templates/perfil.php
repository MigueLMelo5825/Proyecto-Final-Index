<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);

if (!isset($usuario)) {
    $usuario = [
        'nombre' => 'Usuario invitado',
        'bio' => '',
        'foto' => 'web/img/default.png'
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
            <img src="<?= $usuario['foto'] ?>" alt="Foto de perfil">
            <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
            <p><?= htmlspecialchars($usuario['bio']) ?></p>

            <div id="estadisticas">
                <div class="stat"><strong>32</strong><span>Libros leídos</span></div>
                <strong><?= $numeroListas ?></strong>
                <div class="stat"><strong>14</strong><span>Reseñas</span></div>
            </div>
        </section>

        <hr>

        <div class="bootstrap-zone">
            <div class="container-fluid mt-5">

                <div class="row g-3 mb-4">

                    <!-- TOP LIBROS -->
                    <div class="col-lg-4">
                        <div class="p-3 border rounded bg-light h-100">
                            <h4 class="text-center mb-3">📚 Top 5 Libros</h4>
                            <div class="row g-2">
                                <?php foreach ($topLibros as $libro): ?>
                                    <div class="col-6 col-md-6">
                                        <div class="top-item">
                                            <img src="<?= $libro['imagen_url'] ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
                                            <strong><?= htmlspecialchars($libro['titulo']) ?></strong>
                                            <small><?= htmlspecialchars($libro['autores']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- TOP PELÍCULAS -->
                    <div class="col-lg-4">
                        <div class="p-3 border rounded bg-light h-100">
                            <h4 class="text-center mb-3">🎬 Top 5 Películas</h4>
                            <div class="row g-2">
                                <?php foreach ($topPeliculas as $peli): ?>
                                    <div class="col-6 col-md-6">
                                        <div class="top-item">
                                            <img src="<?= $peli['portada'] ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>">
                                            <strong><?= htmlspecialchars($peli['titulo']) ?></strong>
                                            <small><?= htmlspecialchars($peli['genero_nombre']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                   <!-- MIS LISTAS -->
<div class="col-lg-4">
    <div class="p-4 border rounded bg-light h-100 shadow-sm">
        <h2>Mis listas (<?= $numeroListas ?>)</h2>

        <?php if ($numeroListas === 0): ?>
            <p>No tienes listas creadas todavía.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($listas as $lista): ?>
                    <li>
                        <strong><?= htmlspecialchars($lista['nombre']) ?></strong><br>
                        <em><?= htmlspecialchars($lista['tipo']) ?></em><br>
                        <?= htmlspecialchars($lista['descripcion']) ?><br>
                        <small>Creada el: <?= $lista['creada_en'] ?></small><br>

                        <!-- BOTÓN VER LISTA -->
                        <a href="index.php?ctl=verLista&id=<?= $lista['id'] ?>" 
                           class="btn btn-primary btn-sm mt-2">
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