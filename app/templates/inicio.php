<?php include_once __DIR__ . '/header.php'; ?>

<div class="container mt-5">

    <!-- HERO -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold">Tu espacio para libros y películas</h1>
            <p class="lead">
                Organiza tus listas, guarda tus favoritos y lleva un registro de todo lo que lees y ves.
            </p>

            <div class="mt-4">
                <a href="index.php?ctl=registro" class="btn btn-primary btn-lg me-2">Registrarse</a>
                <a href="index.php?ctl=login" class="btn btn-outline-secondary btn-lg">Iniciar sesión</a>
            </div>
        </div>

        <div class="col-md-6 text-center">
            <!-- Logo INDEX-01 -->
            <img src="<?= $base_url ?>web/img/INDEX-01.png" 
                 alt="INDEX" 
                 class="img-fluid shadow" 
                 style="max-width: 350px;">
        </div>
    </div>

    <!-- FEATURES -->
    <div class="row text-center mb-5">
        <h2 class="mb-4 fw-semibold">¿Qué puedes hacer aquí?</h2>

        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded shadow-sm h-100">
                <h3 class="fs-4">📚 Crear listas</h3>
                <p>Organiza tus libros y películas como quieras: favoritos, pendientes, terminados…</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded shadow-sm h-100">
                <h3 class="fs-4">⭐ Guardar fichas</h3>
                <p>Accede rápidamente a toda la información de cada obra.</p>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded shadow-sm h-100">
                <h3 class="fs-4">🎬 Descubrir contenido</h3>
                <p>Explora nuevas recomendaciones y amplía tu biblioteca personal.</p>
            </div>
        </div>
    </div>

    <!-- CTA FINAL -->
    <div class="text-center py-5 bg-light rounded shadow-sm">
        <h2 class="fw-bold">¿Listo para empezar?</h2>
        <p class="lead">Crea tu cuenta y empieza a construir tus listas.</p>
        <a href="index.php?ctl=registro" class="btn btn-primary btn-lg">Crear cuenta</a>
    </div>

</div>

<?php include_once __DIR__ . '/footer.php'; ?>
