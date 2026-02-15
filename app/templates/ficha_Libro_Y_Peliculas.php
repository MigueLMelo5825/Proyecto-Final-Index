<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['id_usuario'] ?? null;

//codigo php para obtener las rutas y darles la direccion correcta
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/';

//url para cargar las rutas de stylos e imagenes
$urlImgFallback = $root . 'web/img/fallback.png';
$urlFotoUsuario = "";

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

//codigo para pintar el corazon en caso de que el usuario ya haya dado like, la calificacion y calcular su promedio
$usuarioHaDadoLike = false;
$valoracionUsuario = null;

if ($idUsuario && $id) {
    $tipoId = ($type === 'libro') ? 'id_libro' : 'id_pelicula';

    $consultaLikes = $conexionBD->prepare("
        SELECT 1
        FROM likes
        WHERE id_usuario = ? AND $tipoId = ?
        LIMIT 1
    ");

    $consultaLikes->execute([$idUsuario, $id]);
    $usuarioHaDadoLike = (bool) $consultaLikes->fetchColumn();

    $consultaCalificaciones = $conexionBD->prepare("
        SELECT puntuacion
        FROM calificaciones
        WHERE id_usuario = ? AND $tipoId = ?
        LIMIT 1
    ");
    $consultaCalificaciones->execute([$idUsuario, $id]);
    $valoracionUsuario = $consultaCalificaciones->fetchColumn();
}
//calculamos el promedio de las calificaciones
$consultaPromedio = $conexionBD->prepare("SELECT AVG(puntuacion) FROM calificaciones WHERE $tipoId = ?");
$consultaPromedio->execute([$id]);
$promedioRaw = $consultaPromedio->fetchColumn();
$promedio = $promedioRaw === null ? 0.0 : round((float)$promedioRaw, 1);

//calculamos total likes
$conusltaTotalLikes = $conexionBD->prepare("SELECT COUNT(*) FROM likes WHERE $tipoId = ?");
$conusltaTotalLikes->execute([$id]);
$totalLikes = $conusltaTotalLikes->fetchColumn();

//codigo para obtener los comentarios en la base de datos
$comentarios = [];
if ($id) {
    $tipoId = ($type === 'libro') ? 'id_libro' : 'id_pelicula';
    $stmt = $conexionBD->prepare("
        SELECT c.id AS id_comentario, c.usuario_id, c.texto, c.fecha,
               u.username, u.foto, p.nombre AS pais,
               (c.usuario_id = :idUsuario) AS esPropio
        FROM comentarios c
        LEFT JOIN usuarios u ON c.usuario_id = u.id
        INNER JOIN paises p ON u.pais = p.id_pais
        WHERE c.$tipoId = :lpId
        ORDER BY c.fecha DESC
    ");
    $stmt->execute([
        ':idUsuario' => $idUsuario ?? 0,
        ':lpId' => $id
    ]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

function escaparHTML(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

//validamos segun el tipo trae la imagen de portada del objeto 
if ($type === "libro") {
    $urlImagenPortada = $libroPelicula['imagen_url'] ?? '';
} else {
    $urlImagenPortada = $libroPelicula['portada'] ?? '';
}

if ($urlImagenPortada) {
    $urlImagenPortada = str_replace('http://', 'https://', $urlImagenPortada);
}

//validamos que si no encuentra un valor ponemos nuestra imagen preterminada
if (!$urlImagenPortada) {
    $urlImagenPortada = $urlImgFallback;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= escaparHTML($libroPelicula['titulo']) ?></title>
    <link rel="stylesheet" href="<?php echo $root ?>web/css/fichaLibroPelicula.css">
    <link rel="stylesheet" href="web/css/styleFuentes.css">
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


                if ($idUsuario):
                    $listaModel = new ListaModel();
                    $listasUsuario = $listaModel->obtenerListasPorUsuario($idUsuario);
                ?>
                    <div class="añadir-lista">
                        <form action="index.php?ctl=anadir" method="POST">

                            <input type="hidden" name="id_libro" value="<?= $type === 'libro' ? $id : '' ?>">
                            <input type="hidden" name="id_pelicula" value="<?= $type === 'pelicula' ? $id : '' ?>">

                            <label>Añadir a una lista:</label>
                            <select name="id_lista" required>
                                <option value="">Selecciona una lista</option>

                                <?php foreach ($listasUsuario as $lista): ?>
                                    <option value="<?= $lista['id'] ?>">
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

            <!-- Columna Información -->
            <div class="contenido">
                <h2><?= escaparHTML($libroPelicula['titulo']) ?></h2>

                <!-- BARRA DE VALORACIÓN SOCIAL -->
                <div class="rating-social-bar">
                    <div class="estrellas-voto">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input
                                type="radio"
                                name="star"
                                value="<?= $i ?>"
                                id="star<?= $i ?>"
                                <?= ($valoracionUsuario == $i) ? 'checked' : '' ?>>
                            <label for="star<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                    <span class="puntuacion-texto" id="calificacion"><?= number_format($promedio, 1) ?> de 5</span>

                    <button id="btn-favorito" class="btn-interaccion-like <?= $usuarioHaDadoLike ? 'active' : '' ?>" title="Añadir a mis favoritos">
                        <span class="corazon-icono">❤</span>
                    </button>
                    <?php if ($totalLikes > 0): ?>
                        <span id="contador-likes" class="badge-likes">&nbsp;A <?php echo $totalLikes ?> Usuarios les gusta
                            <?php if ($type === "libro"): ?>
                                este Libro
                            <?php else: ?>
                                esta Pelicula
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
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
                        <?php if ($libroPelicula['idioma'] === "es"): ?>
                            <p><strong>Idioma:</strong> <?= escaparHTML("Español" ?? 'N/A') ?></p>
                        <?php else: ?>
                            <p><strong>Idioma:</strong> <?= escaparHTML("Ingles" ?? 'N/A') ?></p>
                        <?php endif ?>
                    </div>

                    <div class="seccion-descripcion">
                        <h3>Descripción</h3>
                        <div id="descripcion-texto" class="texto-recortado">
                            <p><?= nl2br(escaparHTML($libroPelicula['descripcion'] ?? 'Sin descripción')) ?></p>
                        </div>
                        <button id="btn-leer-mas" class="btn-expandir">Leer más</button>
                    </div>
                <?php endif; ?>

                <!-- Mismo bloque para Película pero con sus datos -->
                <?php if ($type === "pelicula"): ?>
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

                <!-- La comunidad se crea dinámicamente -->
                <?php if (count($comentarios) > 0): ?>
                    <section class="panel-comunidad" style="display: block;">
                    <?php else: ?>
                        <section class="panel-comunidad" style="display: none;">
                        <?php endif; ?>
                        <?php if (!empty($comentarios)): ?>
                            <div class="comunidad">
                                <h3>Comunidad</h3>
                                <div class="lista-comentarios">
                                    <?php foreach ($comentarios as $c): ?>
                                        <div class="comentario-item" data-id="<?= $c['id_comentario'] ?>">
                                            <img src="<?= $root . $c['foto'] ?>" class="img-perfil-mini">
                                            <div class="comentario-cuerpo">
                                                <strong><?= htmlspecialchars($c['username']) ?></strong>
                                                <small><?= htmlspecialchars($c['pais']) ?></small>
                                                <p class="texto-comentario"><?= htmlspecialchars($c['texto']) ?></p>
                                                <small class="tiempo-relativo" data-fecha="<?= $c['fecha'] ?>"><?= $c['fecha'] ?></small>
                                                <?php if ($c['esPropio']): ?>
                                                    <div class="acciones-comentario">
                                                        <button class="btn-editar" data-id="<?= $c['id_comentario'] ?>">Editar</button>
                                                        <button class="btn-eliminar" data-id="<?= $c['id_comentario'] ?>">Eliminar</button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        </section>


                        <!-- SECCIÓN COMENTARIOS DARK -->
                        <section class="panel-comentarios">
                            <h3>Haz un comentario</h3>

                            <form class="form-post">
                                <textarea placeholder="¿Qué te ha parecido?..." required></textarea>
                                <button type="submit" class="btn-publicar">Publicar</button>
                            </form>

                            <p class="msj-vacio" style="display: <?= !$comentarios ? 'block' : 'none' ?>;">Sé el primero en comentar</p>

                        </section>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo $root ?>web/js/fichaLibroPelicula.js"></script>
</body>

</html>