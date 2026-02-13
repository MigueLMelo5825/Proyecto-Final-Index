<?php if (!isset($eventos)) $eventos = []; ?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title> Home </title>

  <link rel="stylesheet" href="web/css/styleTimeline.css">
  <link rel="stylesheet" href="web/css/styleFuentes.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="./web/js/frases.js"></script>
</head>

<body>

  <div class="home-page">

    <!-- HEADER -->
    <?php require __DIR__ . "/header.php"; ?>

    <!-- BIENVENIDA -->
    <section class="home-welcome py-4">
      <div class="container">
        <div class="p-4 rounded-4 welcome-card">
          <h2 class="fw-bold">
            Bienvenid@, <?= htmlspecialchars($_SESSION['usuarioNombre'] ?? 'Usuario') ?>
          </h2>
        </div>
      </div>
    </section>

    <!-- PELÍCULAS RECOMENDADAS -->
    <?php if (!isset($topPeliculas)) $topPeliculas = []; ?>

    <section class="pb-4">
      <div class="container">

        <div class="d-flex align-items-center justify-content-center mb-4">
          <h3 class="fw-bold mb-0">Películas recomendadas</h3>
        </div>

        <?php if (empty($topPeliculas)): ?>
          <div class="bg-body-tertiary rounded-4 p-4">
            No se pudo cargar la recomendación de películas.
          </div>
        <?php else: ?>

          <?php $chunks = array_chunk($topPeliculas, 4); ?>

          <div class="carousel-inner bg-body-tertiary p-3 p-md-4">
            <?php foreach ($chunks as $i => $grupo): ?>
              <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="row g-3">

                  <?php foreach ($grupo as $peli): ?>
                    <div class="col-6 col-md-3">
                      <div class="card h-100 recom-card">

                        <img
                          src="<?= htmlspecialchars($peli['portada'] ?? 'web/img/fallback.png') ?>"
                          class="card-img-top"
                          alt="Portada"
                          style="height:340px; object-fit:cover;">

                        <div class="card-body">
                          <h6 class="card-title fw-bold mb-1">
                            <?= htmlspecialchars($peli['titulo'] ?? '') ?>
                          </h6>

                          <div class="text-secondary small mb-2">
                            <?= htmlspecialchars($peli['anio'] ?? '') ?>
                          </div>

                          <a class="btn btn-sm text-white"
                            style="background-color: var(--accent);"
                            href="index.php?ctl=fichaLibroPelicula&id=<?= (int)($peli['id'] ?? 0) ?>&type=pelicula">
                            Ver
                          </a>
                        </div>

                      </div>
                    </div>
                  <?php endforeach; ?>

                </div>
              </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    </section>

     <!-- LIBROS RECOMENDADOS -->
    <?php if (!isset($topLibros)) $topLibros = []; ?>

    <section class="pb-4">
      <div class="container">

        <div class="d-flex align-items-center justify-content-center mb-4">
          <h3 class="fw-bold mb-0">Libros recomendados</h3>
        </div>

        <?php if (empty($topLibros)): ?>
          <div class="bg-body-tertiary rounded-4 p-4">
            No se pudo cargar la recomendación de libros.
          </div>
        <?php else: ?>

          <?php $chunks = array_chunk($topLibros, 4); ?>

          <div class="carousel-inner bg-body-tertiary p-3 p-md-4">
            <?php foreach ($chunks as $i => $grupo): ?>
              <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="row g-3">

                  <?php foreach ($grupo as $libro): ?>
                    <div class="col-6 col-md-3">
                      <div class="card h-100 recom-card">

                        <img
                          src="<?= htmlspecialchars($libro['imagen_url'] ?? 'web/img/fallback.png') ?>"
                          class="card-img-top"
                          alt="Portada"
                          style="height:340px; object-fit:cover;">

                        <div class="card-body">
                          <h6 class="card-title fw-bold mb-1">
                            <?= htmlspecialchars($libro['titulo'] ?? '') ?>
                          </h6>

                          <?php
                          $autores = $libro['autores'] ?? '';
                          $autoresCorto = mb_strimwidth($autores, 0, 40, '...');
                          ?>
                          <div class="text-secondary small mb-2">
                            <?= htmlspecialchars($autoresCorto) ?>
                          </div>


                          <a class="btn btn-sm text-white"
                            style="background-color: var(--accent);"
                            href="index.php?ctl=fichaLibroPelicula&id=<?= htmlspecialchars($libro['id']) ?>&type=libro">
                            Ver
                          </a>
                        </div>

                      </div>
                    </div>
                  <?php endforeach; ?>

                </div>
              </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    </section>

   <!-- ACTIVIDAD + CURIOSIDADES -->
<main>
  <div class="home-layout">

    <!-- COLUMNA IZQUIERDA (2/3) -->
    <section class="timeline-col">
      <section class="timeline">
        <h2>Actividad reciente</h2>

        <?php if (empty($eventos)): ?>
          <p>No hay actividad reciente.</p>
        <?php else: ?>

          <?php foreach ($eventos as $evento): ?>
            <div class="evento <?= $evento['id_usuario'] == $_SESSION['id_usuario'] ? 'propio' : '' ?>">

              <div class="evento-avatar">
                <?php
                $foto = trim($evento['foto'] ?? '');
                if ($foto === '' || !file_exists($foto)) {
                  $foto = 'web/img/perfil/default.png';
                }
                ?>
                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto de perfil">
              </div>

              <div class="evento-body">

                <div class="evento-header">
                  <a class="evento-usuario"
                     href="index.php?ctl=perfil&id=<?= $evento['id_usuario'] ?>">
                    @<?= htmlspecialchars($evento['username']) ?>
                  </a>

                  <span class="evento-icon">
                    <?php
                    $iconos = [
                      'registro'      => '👤',
                      'lista_creada'  => '📝',
                      'libro'         => '📚',
                      'pelicula'      => '🎬',
                      'login'         => '🔐'
                    ];
                    echo $iconos[$evento['tipo']] ?? '⭐';
                    ?>
                  </span>

                  <span class="tag"><?= htmlspecialchars($evento['tipo']) ?></span>
                  <span class="evento-fecha"><?= $evento['fecha'] ?></span>
                </div>

                <div class="evento-contenido">
                  <h4><?= htmlspecialchars($evento['titulo']) ?></h4>

                  <p><?= htmlspecialchars(FormatearEventos::generarDescripcion($evento, $_SESSION['id_usuario'])) ?></p>
                </div>

              </div>
            </div>
          <?php endforeach; ?>

        <?php endif; ?>
      </section>
    </section>

    <!-- COLUMNA DERECHA (1/3) -->
    <aside class="curiosidades-col">

<div class="ext-card p-4 mb-4 text-center curiosidad-card">
  <div id="hoyHistoria">Cargando historia del día...</div>
</div>

<div class="ext-card p-4 text-center curiosidad-card">
  <div id="sabiasQue">Cargando curiosidad...</div>
</div>


    </aside>

  </div>
</main>

    <!-- FRASE LITERARIA -->
    <div class="container my-4">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="ext-card p-4 text-center">
            <h5 class="mb-3">Frase literaria</h5>

            <blockquote id="quoteText" class="mb-3 fs-5"></blockquote>
            <div id="quoteAuthor" class="text-secondary small"></div>

            <button class="btn btn-outline-dark btn-sm mt-3" id="newQuoteBtn">
              Otra frase
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <?php require __DIR__ . "/footer.php"; ?>

  </div>
<script src="web/js/apiWikipedia.js"></script>

</body>

</html>