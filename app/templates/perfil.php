<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);

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

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="web/css/styleperfil.css">
</head>
<body>
    
<?php include_once __DIR__.'/../templates/header.php'; ?>

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

    <!-- ZONA AISLADA PARA BOOTSTRAP -->
    <div class="bootstrap-zone">

        <div class="container-fluid mt-5">
            <div class="row g-5">

                <!-- IZQUIERDA: TOP LIBROS + TOP PELIS -->
                <div class="col-lg-8">
                    <div class="row g-4">

                        <!-- TOP LIBROS -->
                        <div class="col-md-6">
                            <div class="p-4 border rounded bg-light h-100 shadow-sm">
                                <h2 class="text-center mb-4">📚 Top 5 Libros</h2>

                                <?php foreach ($topLibros as $libro): ?>
                                    <div class="d-flex mb-3 p-2 border rounded bg-white shadow-sm">
                                        <img src="<?= $libro['imagen_url'] ?>" 
                                             class="me-3 rounded"
                                             style="width:60px; height:90px; object-fit:cover;">
                                        <div>
                                            <strong><?= $libro['titulo'] ?></strong><br>
                                            <small><?= $libro['autores'] ?></small><br>
                                            <small><?= $libro['categoria'] ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- TOP PELÍCULAS -->
                        <div class="col-md-6">
                            <div class="p-4 border rounded bg-light h-100 shadow-sm">
                                <h2 class="text-center mb-4">🎬 Top 5 Películas</h2>

                                <?php foreach ($topPeliculas as $peli): ?>
                                    <div class="d-flex mb-3 p-2 border rounded bg-white shadow-sm">
                                        <img src="<?= $peli['portada'] ?>" 
                                             class="me-3 rounded"
                                             style="width:60px; height:90px; object-fit:cover;">
                                        <div>
                                            <strong><?= $peli['titulo'] ?></strong><br>
                                            <small><?= $peli['genero_nombre'] ?></small><br>
                                            <small><?= $peli['anio'] ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <a href="index.php?ctl=crearLista" class="btn btn-primary w-100 mt-3">
                                    Crear nueva lista
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- DERECHA: MIS LISTAS -->
                <div class="col-lg-4">
                    <div class="p-4 border rounded bg-light h-100 shadow-sm">
                        <h2 class="text-center mb-4">📝 Mis listas</h2>

                        <?php if (!empty($listas)): ?>
                            <?php foreach ($listas as $lista): ?>
                                <div class="p-3 mb-3 border rounded bg-white shadow-sm">
                                    <h5><?= htmlspecialchars($lista['nombre']) ?></h5>
                                    <p class="mb-1"><?= htmlspecialchars($lista['descripcion']) ?></p>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($lista['tipo']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No tienes listas creadas todavía.</p>
                        <?php endif; ?>

                        <a href="index.php?ctl=crearLista" class="btn btn-success w-100 mt-3">
                            Crear nueva lista
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div> <!-- FIN bootstrap-zone -->

</main>

<?php include_once __DIR__.'/../templates/footer.php'; ?>
</body>
</html>
