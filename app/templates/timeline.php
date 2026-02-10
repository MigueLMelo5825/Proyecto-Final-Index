<?php if (!isset($eventos)) $eventos = []; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actividad reciente</title>
    <link rel="stylesheet" href="web/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="web/css/styleBase.css">
    <link rel="stylesheet" href="web/css/styleLayout.css">
    <link rel="stylesheet" href="web/css/styleComponents.css">
    <link rel="stylesheet" href="web/css/styleTimeline.css">

</head>

<body>

    <!-- =========================
     HEADER GLOBAL
     ========================= -->
    <?php require __DIR__ . "/header.php"; ?>


    <!-- =========================
     CONTENIDO PRINCIPAL
     ========================= -->
    <main>
        <section class="timeline">
            <h2 class="mb-4">Actividad reciente</h2>

            <?php if (count($eventos) === 0): ?>
                <p>No hay actividad reciente.</p>

            <?php else: ?>
                <?php foreach ($eventos as $evento): ?>

                    <div class="evento">

                        <!-- Avatar -->
                        <div class="evento-avatar">
                            <img
                                src="<?= !empty($evento['foto'])
                                            ? htmlspecialchars($evento['foto'])
                                            : 'web/img/default.png' ?>"
                                alt="Foto de perfil">
                        </div>


                        <!-- Cuerpo del evento -->
                        <div class="evento-body">

                            <!-- Cabecera -->
                            <div class="evento-header">
                                <a class="evento-usuario" href="index.php?ctl=perfil&id=<?= $evento['id_usuario'] ?>">
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

                                <span class="evento-fecha"><?= $evento['fecha'] ?></span>
                            </div>

                            <!-- Contenido -->
                            <div class="evento-contenido">
                                <h4><?= htmlspecialchars($evento['titulo']) ?></h4>

                                <?php
                                $descripcion = $evento['descripcion'];
                                if ($evento['id_usuario'] != $_SESSION['id_usuario']) {
                                    $descripcion = preg_replace('/^Has\b/i', 'Ha', $descripcion);
                                }
                                ?>

                                <p><?= htmlspecialchars($descripcion) ?></p>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>
    </main>


    <!-- =========================
     FOOTER GLOBAL
     ========================= -->
    <?php require __DIR__ . "/footer.php"; ?>


    <!-- Bootstrap JS -->
    <script src="web/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>