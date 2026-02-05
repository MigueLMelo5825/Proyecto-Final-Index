<footer class="main-footer bg-dark text-light mt-5 py-4">
    <div class="container">
        <div class="row">

            <div class="col-md-4 mb-3">
                <h3 class="h5">INDEX</h3>
                <p>Tu biblioteca personal de libros y películas.</p>
            </div>

            <div class="col-md-4 mb-3">
                <h4 class="h6">Enlaces</h4>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-light text-decoration-none">Inicio</a></li>
                    <li><a href="index.php?controller=usuario&action=perfil" class="text-light text-decoration-none">Mi Perfil</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-3">
                <h4 class="h6">Contacto</h4>
                <p>Soporte: info@proyectoindex.com</p>
                <div>
                    <a href="#" class="text-light me-3">Instagram</a>
                    <a href="#" class="text-light">Twitter</a>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <p class="mb-0">&copy; <?= date("Y"); ?> Proyecto Final Index - Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para el buscador -->
 <!-- En footer.php -->
<?php
    // Calculamos la ruta base de forma dinámica
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    // Esto obtiene "/nombre_de_tu_carpeta_sea_cual_sea/"
    $project_root = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . $host . $project_root;
?>

<script src="<?= $base_url ?>web/js/buscadorLibrosYPeliculas.js" defer></script>

</body>
</html>
