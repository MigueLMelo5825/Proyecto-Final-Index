<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['id_usuario'] ?? null;

// Código para obtener las rutas correctas
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/' . trim($root, '/') . '/';

// Imagen fallback
$urlImgFallback = $root . 'web/img/fallback.png';

// Variables de control
$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';

if ($id === '' || $type === '') {
    http_response_code(400);
    echo json_encode(["error" => "ID o Type faltantes"]);
    exit;
}

$conexionBD = Database::getConnection();
$libroPelicula = Libros::obtenerLibroPelicula($conexionBD, $id, $type);

// Obtener género si es película
$genero = null;
if ($type === "pelicula" && isset($libroPelicula['genero'])) {
    $genero = Peliculas::obtenerNombreGenero($libroPelicula['genero']);
}

if (!$libroPelicula) {
    http_response_code(404);
    echo "<h1>Libro no encontrado</h1>";
    exit;
}

function escaparHTML(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Imagen de portada
$urlImagenPortada = $type === "libro" ? $libroPelicula['imagen_url'] ?? '' : $libroPelicula['portada'] ?? '';
if ($urlImagenPortada) {
    $urlImagenPortada = str_replace('http://', 'https://', $urlImagenPortada);
}
if (!$urlImagenPortada) {
    $urlImagenPortada = $urlImgFallback;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= escaparHTML($libroPelicula['titulo']) ?></title>
    <link rel="stylesheet" href="<?= $root ?>web/css/fichaLibroPelicula.css">
</head>

<body>

    <?php include_once __DIR__ . '/../templates/header.php'; ?>

    <main class="detalle">
        <div class="detalle-grid">
            <!-- Columna Portada -->
            <div class="portada">
                <div class="portada-wrapper">
                    <img src="<?= escaparHTML($urlImagenPortada) ?>" alt="<?= escaparHTML($libroPelicula['titulo']) ?>">
                </div>

                <?php
                require_once dirname(__DIR__) . '/Models/ListaModel.php';

                if ($idUsuario):
                    $listasUsuario = ListaModel::obtenerListasUsuario($conexionBD, $idUsuario);
                ?>
                    <div class="añadir-lista">
                        <form action="index.php?ctl=añadirALista" method="POST">
                            <input type="hidden" name="id_libro" value="<?= $type === 'libro' ? $id : '' ?>">
                            <input type="hidden" name="id_pelicula" value="<?= $type === 'pelicula' ? $id : '' ?>">

                            <label>Añadir a una lista:</label>
                            <select name="id_lista" required>
                                <option value="">Selecciona una lista</option>
                                <?php foreach ($listasUsuario as $lista): ?>
                                    <option value="<?= $lista['id'] ?>">
                                        <?= escaparHTML($lista['nombre']) ?>
                                    </option>

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
                    <div class="añadir-lista">
                        <a href="index.php?ctl=login" class="boton-externo">
                            Inicia sesión para añadir a una lista
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna Información -->
            <div class="contenido">
                <h2><?= escaparHTML($libroPelicula['titulo']) ?></h2>

                <!-- Barra de valoración -->
                <div class="rating-social-bar">
                    <div class="estrellas-voto">
                        <input type="radio" name="star" id="star5"><label for="star5">★</label>
                        <input type="radio" name="star" id="star4"><label for="star4">★</label>
                        <input type="radio" name="star" id="star3"><label for="star3">★</label>
                        <input type="radio" name="star" id="star2"><label for="star2">★</label>
                        <input type="radio" name="star" id="star1"><label for="star1">★</label>
                    </div>
                    <span class="puntuacion-texto">4.8 de 5</span>
                    <button id="btn-favorito" class="btn-interaccion-like" title="Añadir a mis favoritos">
                        <span class="corazon-icono">❤</span>
                    </button>
                </div>

                <?php if (!empty($libroPelicula['subtitulo'])): ?>
                    <h3 class="subtitulo-ficha"><?= escaparHTML($libroPelicula['subtitulo']) ?></h3>
                <?php endif; ?>

                <?php if ($type === "libro"): ?>
                    <div class="info-tecnica">
                        <p><strong>Autor(es):</strong> <?= escaparHTML($libroPelicula['autores'] ?? 'Desconocido') ?></p>
                        <p><strong>Categoría:</strong> <?= escaparHTML($libroPelicula['categoria'] ?? 'N/A') ?></p>
                        <p><strong>Editorial:</strong> <?= escaparHTML($libroPelicula['editorial'] ?? 'N/A') ?></p>
                        <p><strong>Fecha:</strong> <?= escaparHTML($libroPelicula['fecha_publicacion'] ?? 'N/A') ?></p>
                        <p><strong>Páginas:</strong> <?= escaparHTML($libroPelicula['paginas'] ?? 'N/A') ?></p>
                        <p><strong>Idioma:</strong> <?= $libroPelicula['idioma'] === "es" ? "Español" : "Inglés" ?></p>
                    </div>

                    <div class="seccion-descripcion">
                        <h3>Descripción</h3>
                        <div id="descripcion-texto" class="texto-recortado">
                            <p><?= nl2br(escaparHTML($libroPelicula['descripcion'] ?? 'Sin descripción')) ?></p>
                        </div>
                        <button id="btn-leer-mas" class="btn-expandir">Leer más</button>
                    </div>
                <?php else: ?>
                    <div class="info-tecnica">
                        <p><strong>Año:</strong> <?= escaparHTML($libroPelicula['anio'] ?? 'N/A') ?></p>
                        <p><strong>Género:</strong> <?= escaparHTML($genero ?? 'N/A') ?></p>
                    </div>
                    <div class="seccion-descripcion">
                        <h3>Descripción</h3>
                        <div id="descripcion-texto" class="texto-recortado">
                            <p><?= nl2br(escaparHTML($libroPelicula['descripcion'] ?? 'Sin descripción')) ?></p>
                        </div>
                        <button id="btn-leer-mas" class="btn-expandir">Leer más</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../templates/footer.php'; ?>
    <script src="<?= $root ?>web/js/fichaLibroPelicula.js"></script>
</body>

</html>