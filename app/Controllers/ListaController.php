<?php

class ListaController {

    private $session;

    public function __construct($session) {
        $this->session = $session;
    }

    // ============================================================
    // CREAR LISTA
    // ============================================================
    public function crear() {

        $this->session->checkSecurity();
        $idUsuario = $this->session->get('id_usuario');

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

        require __DIR__ . '/../templates/crear_lista.php';
    }

    // ============================================================
    // AÑADIR LIBRO/PELÍCULA A UNA LISTA (CÓDIGO ANTIGUO)
    // ============================================================
    public function añadir() {

        $this->session->checkSecurity();
        $idUsuario = $this->session->get('id_usuario');

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

    // ============================================================
    // VER UNA LISTA (NUEVO)
    // ============================================================
    public function ver()
    {
        $idLista = $_GET['id'] ?? null;

        if (!$idLista) {
            echo "<h2>Error: lista no encontrada.</h2>";
            return;
        }

        $conexion = Database::getConnection();

        // Obtener datos de la lista
        $sql = "SELECT * FROM listas WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista]);
        $lista = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lista) {
            echo "<h2>La lista no existe.</h2>";
            return;
        }

        // Obtener elementos de la lista
        $items = ListaItemsModel::obtenerItems($conexion, $idLista);

        require __DIR__ . '/../templates/ver_lista.php';
    }

    // ============================================================
    // AÑADIR ELEMENTO MANUAL A UNA LISTA (NUEVO)
    // ============================================================
    public function agregarItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "<h2>Acceso no permitido</h2>";
            return;
        }

        $idLista = $_POST['id_lista'] ?? null;
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? null;

        if (!$idLista || $titulo === '') {
            echo "<h2>Datos incompletos</h2>";
            return;
        }

        $conexion = Database::getConnection();
        ListaItemsModel::agregarItem($conexion, $idLista, $titulo, $descripcion);

        header("Location: index.php?ctl=verLista&id=" . $idLista);
        exit;
    }
}
