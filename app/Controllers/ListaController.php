<?php

class ListaController {

    // -------------------------------------------------------------
    // CREAR LISTA
    // -------------------------------------------------------------
    public function crear() {
        $session = new SessionManager();
        $session->checkSecurity();

        $idUsuario = $session->get('id_usuario');

        // Si envían el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo = trim($_POST['tipo'] ?? 'personal');

            if ($nombre === '') {
                header("Location: index.php?ctl=crearLista&error=nombre_vacio");
                exit;
            }

            $modelo = new ListaModel();
            $modelo->crearLista($idUsuario, $nombre, $descripcion, $tipo);

            header("Location: index.php?ctl=perfil");
            exit;
        }

        // Cargar vista del formulario
        require __DIR__ . '/../templates/crear_lista.php';
    }

    // -------------------------------------------------------------
    // AÑADIR LIBRO/PELÍCULA A UNA LISTA
    // -------------------------------------------------------------
    public function añadir() {
        $session = new SessionManager();
        $session->checkSecurity();

        $idUsuario = $session->get('id_usuario');

        $idLista = $_POST['id_lista'] ?? null;
        $idLibro = $_POST['id_libro'] ?? null;
        $idPelicula = $_POST['id_pelicula'] ?? null;

        if (!$idLista || (!$idLibro && !$idPelicula)) {
            header("Location: index.php?ctl=perfil");
            exit;
        }

        $conexion = Database::getConnection();

        $sql = "INSERT INTO listas_items (id_lista, id_libro, id_pelicula, añadido_en)
                VALUES (?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista, $idLibro, $idPelicula]);

        header("Location: index.php?ctl=perfil");
        exit;
    }
}
