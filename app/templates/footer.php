<style>
   


.main-footer a{
    transition: all 0.25s ease;
}


.main-footer .d-flex a:hover{
    color: #0d6efd !important;
    transform: translateX(5px);
}



.main-footer img{
    transition: 0.3s ease;
}

.main-footer img:hover{
    transform: scale(1.05);
    opacity: 0.9;
}

</style>

<footer class="main-footer bg-dark text-light mt-5 py-4">
    <div class="container">
        <div class="row align-items-start">

            <div class="col-md-4 mb-3">
                <h3 class="h5">INDEX</h3>
                <p>Tu biblioteca personal de libros y películas.</p>

                <img src="<?= $base_url ?>web/img/INDEX-02.png" 
                     alt="Logo Index"
                     style="max-width:150px;"
                     class="mb-3">
            </div>

          
            <div class="col-md-4 mb-3">
                <h4 class="h5">CONTACTO</h4>
                <p>Soporte: info@proyectoindex.com</p>




                <div class="d-flex flex-column gap-2 mt-3">
                    <a href="#" class="text-light text-decoration-none">
                        <i class="bi bi-instagram me-2"></i> Instagram
                    </a>

                    <a href="#" class="text-light text-decoration-none">
                        <i class="bi bi-twitter-x me-2"></i> Twitter
                    </a>

                    <a href="#" class="text-light text-decoration-none">
                        <i class="bi bi-tiktok me-2"></i> TikTok
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <h4 class="h5">SOBRE NOSOTROS</h4>
                <p class="small">
                    INDEX es una plataforma social donde puedes descubrir, 
                    guardar y compartir tus libros y películas favoritas.
                </p>

            </div>

        </div>

        <div class="text-center mt-4">
            <p class="mb-0">
                &copy; <?= date("Y"); ?> Proyecto Final Index - Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>
