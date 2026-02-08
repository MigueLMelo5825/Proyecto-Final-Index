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
            $tipo = trim($_POST['tipo'] ?? 'personal'); // tipo puede ser 'libro', 'pelicula', 'mixta'
            if (!in_array($tipo, ['libro', 'pelicula', 'mixta'])) {
                $tipo = 'mixta';
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
    public function anadir()
    {header('Content-Type: application/json; charset=utf-8');

        $this->session->checkSecurity();

        $idLista    = $_POST['id_lista'] ?? null;
        $idLibro    = !empty($_POST['id_libro']) ? $_POST['id_libro'] : null;
        $idPelicula = !empty($_POST['id_pelicula']) ? $_POST['id_pelicula'] : null;

        header('Content-Type: application/json');

        if (!$idLista || (!$idLibro && !$idPelicula)) {
            echo json_encode(["status" => "error", "mensaje" => "Faltan datos."]);
            exit;
        }

        $conexion = Database::getConnection();

        // Obtener tipo de lista
        $stmt = $conexion->prepare("SELECT tipo FROM listas WHERE id = ?");
        $stmt->execute([$idLista]);
        $lista = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lista) {
            echo json_encode(["status" => "error", "mensaje" => "Lista no encontrada."]);
            exit;
        }

        $tipoLista = $lista['tipo'];

        // Validación híbrida
        if ($tipoLista !== 'mixta') {
            if ($tipoLista === 'libro' && !$idLibro) {
                echo json_encode(["status" => "error", "mensaje" => "Solo puedes añadir libros a esta lista."]);
                exit;
            }
            if ($tipoLista === 'pelicula' && !$idPelicula) {
                echo json_encode(["status" => "error", "mensaje" => "Solo puedes añadir películas a esta lista."]);
                exit;
            }
        }

        // Preparar título y descripción
        $titulo = "";
        $descripcion = "";

        if ($idLibro) {
            $stmt = $conexion->prepare("SELECT titulo, autores FROM libros WHERE id = ?");
            $stmt->execute([$idLibro]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $titulo = $data['titulo'] ?? "Libro desconocido";
            $descripcion = isset($data['autores']) ? "Autor: " . $data['autores'] : "Sin información disponible";
        }

        if ($idPelicula) {
            $stmt = $conexion->prepare("SELECT titulo, anio FROM peliculas WHERE id = ?");
            $stmt->execute([$idPelicula]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $titulo = $data['titulo'] ?? "Película desconocida";
            $descripcion = isset($data['anio']) ? "Año: " . $data['anio'] : "Sin información disponible";
        }

        // Insertar en la lista
        $sql = "INSERT INTO listas_items (id_lista, titulo, descripcion, id_libro, id_pelicula, añadido_en)
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idLista, $titulo, $descripcion, $idLibro, $idPelicula]);

        echo json_encode(["status" => "success", "mensaje" => "Elemento añadido correctamente."]);
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
