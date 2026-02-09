<?php
// Calculamos la ruta base de forma dinámica
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$project_root = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$base_url = $protocol . $host . $project_root;

// Aseguramos acceso al SessionManager si existe
if (!isset($session)) {
    global $session;
}

// Determinamos si hay usuario logueado
$loggedIn = isset($session) ? $session->isLoggedIn() : (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] > 0);

// Elegir URL de destino del logo y enlace "Inicio"
$inicioUrl = $loggedIn ? $base_url . 'index.php?ctl=timeline' : $base_url . 'index.php';

// Elegir logo: INDEX-02 para navbar oscuro, INDEX-01 para fondos claros
$logoUrl = $base_url . 'web/img/INDEX-02.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>web/css/styles.css">
    <link rel="icon" type="image/png" href="web/img/INDEX-01_favicon.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <title>INDEX</title>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand fw-bold" href="<?= $inicioUrl ?>">
            <img src="<?= $logoUrl ?>" alt="INDEX" height="40">
        </a>

        <!-- Botón responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- IZQUIERDA -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $inicioUrl ?>">Inicio</a>
                </li>
            </ul>

            <!-- BUSCADOR -->
            <form class="d-flex me-3 position-relative" role="search">
                <input class="form-control" type="search" placeholder="Buscar..." id="inputLibro">
                <div id="resultadosBusqueda" 
                     class="list-group position-absolute w-100 mt-5"
                     style="z-index: 2000;"></div>
            </form>

            <!-- DERECHA (según sesión) -->
            <ul class="navbar-nav">

                <?php if ($loggedIn): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>index.php?ctl=perfil">
                            <i class="bi bi-person-circle"></i> Perfil
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= $base_url ?>index.php?ctl=cerrarSesion">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>index.php?ctl=registro">
                            <i class="bi bi-person-plus"></i> Registrarse
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>index.php?ctl=login">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
