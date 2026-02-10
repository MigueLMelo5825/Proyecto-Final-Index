<?php if (!isset($eventos)) $eventos = []; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividad reciente</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="web/bootstrap/css/bootstrap.min.css">

    <!-- CSS general -->
    <link rel="stylesheet" href="web/css/style.css">

    <!-- CSS específico del timeline -->
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
                <div class="evento-icon">
                    <?php
                        // Iconos según tipo de evento
                        $iconos = [
                            'registro'      => '👤',
                            'lista_creada'  => '📝',
                            'libro'         => '📚',
                            'pelicula'      => '🎬',
                            'login'         => '🔐'
                        ];
                        echo $iconos[$evento['tipo']] ?? '⭐';
                    ?>
                </div>

                <div class="evento-contenido">
                    <h4><?= htmlspecialchars($evento['titulo']) ?></h4>
                    <p><?= htmlspecialchars($evento['descripcion']) ?></p>
                    <span class="evento-fecha"><?= $evento['fecha'] ?></span>
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
