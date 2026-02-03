<?php
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Models/Libros.php';

//variables de control
$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';

if ($id === '' || $type === '') {
    http_response_code(400);
    echo json_encode(["error" => "ID o Type faltantes"]);
    exit;
}

$conexionBD = Database::getConnection();
$libroPelicula = Libros::obtenerLibroPelicula($conexionBD, $id, $type);

if (!$libroPelicula) {
    http_response_code(404);
    echo "<h1>Libro no encontrado</h1>";
    exit;
}

function escaparHTML(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

$urlImagenPortada = $libroPelicula['imagen_url'] ?? '';
if ($urlImagenPortada) {
    $urlImagenPortada = str_replace('http://', 'https://', $urlImagenPortada);
}
if (!$urlImagenPortada) {
    $urlImagenPortada = '/web/img/fallback.png';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= escaparHTML($libroPelicula['titulo']) ?></title>
    <link rel="stylesheet" href="/web/css/styleperfil.css">
   

    
</head>
<body>

<header>
    <nav>
        <h1>INDEX</h1>
        <ul>
            <li><a href="index.php?controller=usuario&action=perfil">Perfil</a></li>
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<main class="detalle-libro">
    <div class="detalle-grid">

        <div class="portada-libro">
            <img src="<?= escaparHTML($urlImagenPortada) ?>" alt="<?= escaparHTML($libroPelicula['titulo']) ?>">
        </div>

        <div class="contenido-libro">
            <h2><?= escaparHTML($libroPelicula['titulo']) ?></h2>

            <?php if (!empty($libroPelicula['subtitulo'])): ?>
                <h3><?= escaparHTML($libroPelicula['subtitulo']) ?></h3>
            <?php endif; ?>

            <div class="info-libro">
                <p><strong>Autor(es):</strong> <?= escaparHTML($libroPelicula['autores'] ?? 'Desconocido') ?></p>
                <p><strong>Categoría:</strong> <?= escaparHTML($libroPelicula['categoria'] ?? 'N/A') ?></p>
                <p><strong>Editorial:</strong> <?= escaparHTML($libroPelicula['editorial'] ?? 'N/A') ?></p>
                <p><strong>Fecha de publicación:</strong> <?= escaparHTML($libroPelicula['fecha_publicacion'] ?? 'N/A') ?></p>
                <p><strong>Número de páginas:</strong> <?= escaparHTML($libroPelicula['paginas'] ?? 'N/A') ?></p>
                <p><strong>Idioma:</strong> <?= escaparHTML($libroPelicula['idioma'] ?? 'N/A') ?></p>
                <p><strong>ISBN-10:</strong> <?= escaparHTML($libroPelicula['isbn_10'] ?? 'N/A') ?></p>
                <p><strong>ISBN-13:</strong> <?= escaparHTML($libroPelicula['isbn_13'] ?? 'N/A') ?></p>
            </div>

            <div class="descripcion-libro">
                <h3>Descripción</h3>
                <p><?= nl2br(escaparHTML($libroPelicula['descripcion'] ?? 'Sin descripción')) ?></p>
            </div>

            <?php if (!empty($libroPelicula['preview_link'])): ?>
                <a class="boton-externo" 
                   href="<?= escaparHTML($libroPelicula['preview_link']) ?>" 
                   target="_blank" 
                   rel="noopener">
                   Ver en Google Books
                </a>
            <?php endif; ?>

        </div>
    </div>
</main>

</body>
</html>