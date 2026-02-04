<header>
    <!-- Bootstrap CSS -->
<link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet">

<!-- Bootstrap Icons (opcional pero útil) -->
<link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <nav>
        <h1>INDEX</h1>
        <div id="buscador">
            <p>Buscar: <input type="text" id="inputLibro"></p>
            <div id="libroOPeliculaEncontrada"></div>
        </div>
        <ul>
            <li><a href="index.php?controller=usuario&action=perfil">Perfil</a></li>
            <li><a href="index.php?controller=peliculas&action=cargarPeliculas">Cargar Películas</a></li>
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>
</header>