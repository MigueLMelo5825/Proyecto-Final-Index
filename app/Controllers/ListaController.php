<?php

class ListaController
{

    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    // ============================================================
    // CREAR LISTA
    // ============================================================
    public function crear()
    {

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
    // AÑADIR LIBRO/PELÍCULA A UNA LISTA
    // ============================================================
public function anadir() {

    $this->session->checkSecurity();

    $idLista = $_POST['id_lista'] ?? null;
    $idLibro = !empty($_POST['id_libro']) ? (int)$_POST['id_libro'] : null;
    $idPelicula = !empty($_POST['id_pelicula']) ? (int)$_POST['id_pelicula'] : null;

    if (!$idLista || (!$idLibro && !$idPelicula)) {
        echo "Faltan datos";
        exit;
    }

    $conexion = Database::getConnection();

    // LIBRO
    if ($idLibro) {
        $stmt = $conexion->prepare("SELECT titulo, autores FROM libros WHERE id = ?");
        $stmt->execute([$idLibro]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $titulo = $data['titulo'];
            $descripcion = "Autor: " . ($data['autores'] ?? "Desconocido");
        } else {
            $titulo = "Libro desconocido";
            $descripcion = "Sin información disponible";
        }
    }

    // PELÍCULA
    if ($idPelicula) {
        $stmt = $conexion->prepare("SELECT titulo, anio FROM peliculas WHERE id = ?");
        $stmt->execute([$idPelicula]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $titulo = $data['titulo'];
            $descripcion = "Año: " . ($data['anio'] ?? "N/A");
        } else {
            $titulo = "Película desconocida";
            $descripcion = "Sin información disponible";
        }
    }

    // INSERTAR
    $sql = "INSERT INTO listas_items (id_lista, titulo, descripcion, id_libro, id_pelicula, añadido_en)
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$idLista, $titulo, $descripcion, $idLibro, $idPelicula]);

header("Location: index.php?ctl=verLista&id=" . $idLista);
    exit;
}



    // ============================================================
    // VER UNA LISTA
    // ============================================================
    public function ver()
    {
        $this->session->checkSecurity();

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
}
