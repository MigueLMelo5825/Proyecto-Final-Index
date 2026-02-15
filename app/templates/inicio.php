<?php

//codigo php para obtener las rutas y darles la direccion correcta
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/';


include_once __DIR__ . '/header.php'; 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="web/css/style_inicio.css">
    <title>Inicio</title>
</head>
<body>
    

<!-- HERO -->
<section class="hero">
    <div>
        <h1>Libros y películas<br>en un solo lugar</h1>
        <p>
            Descubre, valora y guarda tus libros y películas favoritas.
            Crea listas, sigue a otros usuarios y encuentra nuevas historias.
        </p>
        <div class="hero-actions">
      

        </div>
    </div>
</section>

<!-- LIBROS -->
<section>
    <div class="container">
        <h2 class="section-title">Libros</h2>
        <p class="section-subtitle">Encuentra tu próxima lectura</p>

        <section class="seccion-ranking">
            <h2 style="text-align: center; margin-top: 20px;">⭐ Los mejores Libros valorados por lectores</h2>
            <div id="contenedor-top-libros"></div>
        </section>

        <div class="grid">
            <div class="card-item reveal">
                <div class="big-icon">📚</div>
                <h3>Catálogo amplio</h3>
                <p>Explora miles de libros de todos los géneros.</p>
            </div>

            <div class="card-item reveal">
                <div class="big-icon">⭐</div>
                <h3>Valoraciones</h3>
                <p>Puntúa libros y mira opiniones de otros lectores.</p>
            </div>

            <div class="card-item reveal">
                <div class="big-icon">📝</div>
                <h3>Listas personales</h3>
                <p>Crea listas de lectura y compártelas.</p>
            </div>
        </div>
    </div>
</section>

<!-- PELÍCULAS -->
<section>
    <div class="container">
        <h2 class="section-title">Películas</h2>
        <p class="section-subtitle">Todo el cine que te gusta</p>

        <section class="seccion-ranking">
            <h2 style="text-align: center; margin-top: 50px;">🔥Las Peliculas que más gustan en nuestra comunidad</h2>
            <div id="contenedor-top-peliculas">
            </div>
        </section>
        <div class="grid">
            <div class="card-item reveal">
                <div class="big-icon">🎬</div>
                <h3>Estrenos y clásicos</h3>
                <p>Desde lo último hasta las películas de siempre.</p>
            </div>

            <div class="card-item reveal">
                <div class="big-icon">❤️</div>
                <h3>Favoritas</h3>
                <p>Marca películas y vuelve a ellas cuando quieras.</p>
            </div>

            <div class="card-item reveal">
                <div class="big-icon">👥</div>
                <h3>Comunidad</h3>
                <p>Descubre qué está viendo la gente.</p>
            </div>
        </div>
    </div>
</section>





<script>
    // Reveal on scroll
    const items = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries=>{
        entries.forEach(e=>{
            if(e.isIntersecting){
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, {threshold:0.15});

    items.forEach(el=>observer.observe(el));
</script>



<?php include_once __DIR__ . '/footer.php'; ?>

<script src="<?php echo $root ?>web/js/inicio.js"></script>

</body>
</html>