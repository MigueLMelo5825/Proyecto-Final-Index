<?php
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Models/Libros.php';
require_once dirname(__DIR__) . '/Models/Peliculas.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['id_usuario'] ?? null;

//codigo php para obtener las rutas y darles la direccion correcta
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/' . trim($root, '/') . '/';

//url para cargar las rutas de stylos e imagenes
$urlImgFallback = $root . 'web/img/fallback.png';

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

//funcion para obtener el genero de la pelicula
$genero = null;
if ($type === "pelicula" && isset($libroPelicula['genero'])) {
    $genero = Peliculas::obtenerNombreGenero($libroPelicula['genero']);
}


if (!$libroPelicula) {
    http_response_code(404);
    echo "<h1>Libro no encontrado</h1>";
    exit;
}

function escaparHTML(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

//validamos segun el tipo trae la imagen de portada del objeto 
if($type === "libro"){
    $urlImagenPortada = $libroPelicula['imagen_url'] ?? '';
}else{
    $urlImagenPortada = $libroPelicula['portada'] ?? '';
}

if ($urlImagenPortada) {
    $urlImagenPortada = str_replace('http://', 'https://', $urlImagenPortada);
}

//validamos que si no encuentra un valor ponemos nuestra imagen preterminada
if (!$urlImagenPortada) {
    $urlImagenPortada = $urlImgFallback ;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= escaparHTML($libroPelicula['titulo']) ?></title>
    <link rel="stylesheet" href="../../web/css/fichaLibroPelicula.css">
</head>
<body>

<?php include_once __DIR__.'/../templates/header.php'; ?>

<main class="detalle">
    <div class="detalle-grid">

        <div class="portada">
            <img src="<?= escaparHTML($urlImagenPortada) ?>" alt="<?= escaparHTML($libroPelicula['titulo']) ?>">
        </div>

        <div class="contenido">
            <h2><?= escaparHTML($libroPelicula['titulo']) ?></h2>

            <?php if (!empty($libroPelicula['subtitulo'])): ?>
                <h3><?= escaparHTML($libroPelicula['subtitulo']) ?></h3>
            <?php endif; ?>
            
            <?php if($type === "libro"): ?>
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
            <?php endif; ?>
            <?php if($type === "pelicula"): ?>
                <div class="info-pelicula">
                    <p><strong>A&ntilde;o:</strong> <?= escaparHTML($libroPelicula['anio'] ?? 'N/A') ?></p>
                    <p><strong>Genero:</strong> <?= escaparHTML($genero ?? 'N/A') ?></p>
                </div>

                <div class="descripcion-pelicula">
                    <h3>Descripción</h3>
                    <p><?= nl2br(escaparHTML($libroPelicula['descripcion'] ?? 'Sin descripción')) ?></p>
                </div>
            <?php endif ?>
        </div>
    </div>
</main>

<!-- Botones de Acción -->
<div class="acciones-usuario">  
    <button id="btn-favorito" class="corazon-like" title="Añadir a favoritos">
        <span class="icon">❤</span>
    </button>

<?php
require_once dirname(__DIR__) . '/Models/ListasModel.php';


if ($idUsuario):
$listasUsuario = ListasModel::obtenerListasUsuario($conexionBD, $idUsuario);
?>
    <div class="añadir-lista">
        <form action="index.php?ctl=añadirALista" method="POST">

            <input type="hidden" name="id_libro" value="<?= $type === 'libro' ? $id : '' ?>">
            <input type="hidden" name="id_pelicula" value="<?= $type === 'pelicula' ? $id : '' ?>">

            <label>Añadir a una lista:</label>
            <select name="id_lista" required>
                <option value="">Selecciona una lista</option>

                <?php foreach ($listasUsuario as $lista): ?>
                    <option value="<?= $lista['id_lista'] ?>">
                        <?= escaparHTML($lista['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="boton-externo">Añadir</button>
        </form>

        <a href="index.php?ctl=crearLista" class="boton-externo">
            Crear nueva lista
        </a>
    </div>

<?php else: ?>

    <!-- Mostrar mensaje si NO está logueado -->
    <div class="añadir-lista">
        <a href="index.php?ctl=login" class="boton-externo">
            Inicia sesión para añadir a una lista
        </a>
    </div>

<?php endif; ?>

</div>

<!-- Sección de Valoración -->
<div class="valoracion">
    <p>Valoración:</p>
    <div class="estrellas">
        <input type="radio" name="star" id="star5"><label for="star5">★</label>
        <input type="radio" name="star" id="star4"><label for="star4">★</label>
        <input type="radio" name="star" id="star3"><label for="star3">★</label>
        <input type="radio" name="star" id="star2"><label for="star2">★</label>
        <input type="radio" name="star" id="star1"><label for="star1">★</label>
    </div>
</div>

<!-- Sección de Comentarios -->
<section class="comentarios">
    <h3>Comentarios</h3>
    <form class="form-comentario">
        <textarea placeholder="Escribe tu opinión..."></textarea>
        <button type="submit" class="boton-comentar">Publicar</button>
    </form>
    <div class="lista-comentarios">
        <p class="sin-comentarios">Aún no hay comentarios. ¡Sé el primero!</p>
    </div>
</section>

<?php include_once __DIR__.'/../templates/footer.php'; ?>

</body>
</html>
