<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INDEX – Actividad reciente</title>
    <link rel="stylesheet" href="web/css/styleTimeline.css">
</head>
<body>

<header>
    <nav>
        <h1>Actividad</h1>
        <ul>
<li><a href="index.php?ctl=perfil">Perfil</a></li>
<li><a href="index.php?ctl=listas">Listas</a></li>
<li><a href="index.php?ctl=logout">Salir</a></li>

        </ul>
    </nav>
</header>

<main>

<section id="timeline">
    <h2>Actividad reciente</h2>

    <ul class="timeline-list">

        <?php foreach ($eventos as $evento): ?>
            <li class="timeline-item <?= $evento['tipo'] ?>">
                <span class="timestamp"><?= $evento['fecha'] ?></span>
                <div class="content">
                    <h3><?= $evento['titulo'] ?></h3>
                    <p><?= $evento['descripcion'] ?></p>
                </div>
            </li>
        <?php endforeach; ?>

    </ul>
</section>

</main>

</body>
</html>
