<?php

//codigo php para obtener las rutas y darles la direccion correcta
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/' . trim($root, '/') . '/';

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

// Procesamiento de foto de perfil
$fotoPerfil = trim($usuario['foto'] ?? '');
if ($fotoPerfil === '' || !file_exists($fotoPerfil)) {
    $fotoPerfil = 'web/img/perfil/default.png';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil - <?= htmlspecialchars($usuario['username']) ?></title>
    <link rel="stylesheet" href="web/css/stylePerfil.css">
    <link rel="stylesheet" href="web/css/styleFuentes.css">
</head>
<body class="perfil-page">

<?php include_once __DIR__ . '/../templates/header.php'; ?>

<main class="container-fluid dashboard-layout">
    
    <!-- COLUMNA PRINCIPAL (Izquierda): TU PERFIL ACTUAL -->
    <section class="feed-principal">
        
        <!-- CABECERA DE PERFIL -->
        <section class="perfil-header">
            <div class="perfil-container">
                <div class="perfil-avatar">
                    <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
                </div>

                <div class="perfil-info">
                    <h2><?= htmlspecialchars($usuario['username']) ?></h2>
                    <p class="bio"><?= htmlspecialchars($usuario['bio'] ?? '') ?></p>

                    <div class="perfil-botones">
                        <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                            <a href="index.php?ctl=ajustesPerfil" class="btn-outline">Ajustes</a>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['usuarioNivel']) && $_SESSION['usuarioNivel'] == 3 && $_SESSION['usuarioId'] == $usuario['id']): ?>
                            <a href="index.php?ctl=panelAdmin" class="btn-warning">Panel Admin</a>
                        <?php endif; ?>

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
                            <div><strong>32</strong><span>Libros</span></div>
                            <div><strong><?= $numeroListas ?? 0 ?></strong><span>Listas</span></div>
                            <div><strong>14</strong><span>Reseñas</span></div>
                        </div>
                        <div class="stats-derecha">
                            <a href="index.php?ctl=verSeguidores&id=<?= $usuario['id'] ?>">
                                <strong><?= count($seguidores) ?></strong><span>Seguidores</span>
                            </a>
                            <a href="index.php?ctl=verSeguidos&id=<?= $usuario['id'] ?>">
                                <strong><?= count($seguidos) ?></strong><span>Siguiendo</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENIDO DEL PERFIL -->
        <section class="perfil-contenido">
            <div class="bloques-superiores">
                <!-- TOP LIBROS (Tus variables originales) -->
                <div class="card">
                    <h3>Favoritos del Usuario</h3>
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
                    <h3>Cine Preferido</h3>
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
                                <a href="index.php?ctl=verLista&id=<?= $lista['id'] ?>" class="btn-success">Ver lista</a>
                                <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                                    <a href="index.php?ctl=eliminarLista&id=<?= $lista['id'] ?>" class="btn btn-danger rounded-5 ps-3 pe-3 fw-semibold">Eliminar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($_SESSION['id_usuario'] == $usuario['id']): ?>
                    <a href="index.php?ctl=crearLista" class="btn-success crear-lista">Crear nueva lista</a>
                <?php endif; ?>
            </div>
        </section>
    </section>

    <!-- SIDEBAR DINÁMICO: TENDENCIAS GLOBALES -->
    <aside class="sidebar-tendencias">
        <div class="sticky-sidebar">
            <div class="card-sidebar">
                <h2>Tendencias De La Comunidad</h2><br>
                <h3>🔥 Top 3 Peliculas mas gustadas</h3>
                <div id="top-sidebar-peliculas" class="mini-ranking">
                    <!-- Se carga dinámicamente con JS -->
                </div>
            </div>

            <div class="card-sidebar">
                <h3>⭐ Top 3 Libros Mejor Calificados</h3>
                <div id="top-sidebar-libros" class="mini-ranking">
                    <!-- Se carga dinámicamente con JS -->
                </div>
            </div>
            
            <footer class="footer-mini">
                <p>&copy; 2026 Index.</p>
            </footer>
        </div>
    </aside>

</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>

<!-- Cargamos el JS que gestiona el sidebar dinámico -->
<script src="<?php echo $root ?>web/js/perfil.js"></script>

</body>
</html>