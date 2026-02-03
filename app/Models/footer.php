<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>INDEX</h3>
            <p>Tu biblioteca personal de libros y películas.</p>
        </div>
        
        <div class="footer-section">
            <h4>Enlaces</h4>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="index.php?controller=usuario&action=perfil">Mi Perfil</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contacto</h4>
            <p>Soporte: info@proyectoindex.com</p>
            <div class="social-links">
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Proyecto Final Index - Todos los derechos reservados.</p>
    </div>
</footer>

<?php
// Obtiene la carpeta raíz del proyecto dinámicamente
// Si el proyecto está en localhost/mi_proyecto/ , devolverá /mi_proyecto/
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__, 2)));
$root = '/' . trim($root, '/') . '/';
?>

<script src="<?php echo $root; ?>web/js/buscadorLibrosYPeliculas.js" defer></script>

<?php //if (isset($_GET['controller']) && $_GET['controller'] === 'perfil'): ?>
        <!-- aca se pondran scripts que sean personalizados no generales
<?php //endif; ?>