<?php


class fichaLibroPeliculaController {

    public function ficha() {
        
        require __DIR__ . '/../templates/ficha_Libro_Y_Peliculas.php';
    }

    function guardarLikesYCalificacion(){
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Solo procesamos si hay sesión iniciado
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            echo json_encode(["status" => "error", "mensaje" => "Debes iniciar sesión"]);
            exit;
        }

        // Obtener datos del Fetch (JSON)
        $input = json_decode(file_get_contents("php://input"), true);
        $idLibroPelicula = $input['id'] ?? null;
        $tipo = $input['type'] ?? null; // 'libro' o 'pelicula'
        $accion = $input['accion'] ?? null; // 'like' o 'calificar'
        $puntuacion = $input['puntuacion'] ?? null;

        if (!$idLibroPelicula || !$tipo) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            exit;
        }

        $pdo = Database::getConnection();

        try{
            // Definimos qué columna usar según el tipo
            $tipoId = ($tipo === 'libro') ? 'id_libro' : 'id_pelicula';

            if ($accion === 'like') {
                // Lógica de LIKE: Si existe lo quita (dislike), si no lo pone
                $check = $pdo->prepare("SELECT id_like FROM likes WHERE id_usuario = ? AND $tipoId = ?");
                $check->execute([$idUsuario, $idLibroPelicula]);
                
                if ($check->fetch()) {
                    $stmt = $pdo->prepare("DELETE FROM likes WHERE id_usuario = ? AND $tipoId = ?");
                    $stmt->execute([$idUsuario, $idLibroPelicula]);
                    $res = "eliminado";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO likes (id_usuario, $tipoId, fecha_like) VALUES (?, ?, NOW())");
                    $stmt->execute([$idUsuario, $idLibroPelicula]);
                    $res = "agregado";
                }
                
                // Calculamos el NUEVO TOTAL DE LIKES
                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE $tipoId = ?");
                $stmtCount->execute([$idLibroPelicula]);
                $nuevoTotal = $stmtCount->fetchColumn();

                echo json_encode([
                    "status" => "success", 
                    "resultado" => $res, // "agregado" o "eliminado"
                    "nuevoTotal" => $nuevoTotal 
                ]);
                exit;


            } elseif ($accion === 'calificar' && $puntuacion) {
                // Lógica de CALIFICAR: Insertar o actualizar si ya existe (ON DUPLICATE KEY UPDATE)
                $sql = "INSERT INTO calificaciones (id_usuario, $tipoId, puntuacion) 
                        VALUES (:usuario, :libroPelicula, :puntos) 
                        ON DUPLICATE KEY UPDATE puntuacion = :puntos_update";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':usuario' => $idUsuario,
                    ':libroPelicula' => $idLibroPelicula,
                    ':puntos' => $puntuacion,
                    ':puntos_update' => $puntuacion
                ]);

                // Calculamos el nuevo promedio tras insertar
                $cons = $pdo->prepare("SELECT AVG(puntuacion) FROM calificaciones WHERE $tipoId = ?");
                $cons->execute([$idLibroPelicula]);
                $nuevoPromedio = $cons->fetchColumn();
                $nuevoPromedio = $nuevoPromedio === null ? 0.0 : round((float)$nuevoPromedio, 1);

                echo json_encode([
                    "status" => "success", 
                    "mensaje" => "Calificación guardada",
                    "nuevoPromedio" => $nuevoPromedio ?: '0.0' // Enviamos el dato al JS
                ]);
            }    
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
        }
        exit;
    }

    function guardarComentario(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Solo procesamos si hay sesión iniciado
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            echo json_encode(["status" => "error", "mensaje" => "Debes iniciar sesión"]);
            exit;
        }

        // Obtener datos del Fetch (JSON)
        $input = json_decode(file_get_contents("php://input"), true);
        $idLibroPelicula = $input['id'] ?? null;
        $tipo = $input['type'] ?? null; // 'libro' o 'pelicula'
        $comentario = $input['texto'] ?? null; //comentario

        $comentario = htmlspecialchars(trim($input['texto'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (strlen($comentario) < 2 || strlen($comentario) > 2000) {
            echo json_encode(["status" => "error", "mensaje" => "Comentario inválido (longitud)"]);
            exit;
        }

        if (!$idLibroPelicula || !$tipo) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            exit;
        }

        $pdo = Database::getConnection();

        try {
            // Definimos qué columna usar según el tipo
            $tipoId = ($tipo === 'libro') ? 'id_libro' : 'id_pelicula';

            // Usamos marcadores con nombre para evitar confusiones
            $insertSql = "INSERT INTO comentarios (usuario_id, $tipoId, texto, fecha) 
                    VALUES (:usuario_id, :idLibroPeliula, :comentario, NOW())";
            
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([
                ':usuario_id'    => $idUsuario,
                ':idLibroPeliula' => $idLibroPelicula,
                ':comentario'    => $comentario
            ]);

            // OBTENEMOS LOS DATOS DEL USUARIO (para mostrar el nombre en el feed dinámico)
            $cons = $pdo->prepare("
                SELECT c.id AS id_comentario, c.usuario_id, c.texto, c.fecha, u.username, u.foto, p.nombre AS pais, (c.usuario_id = :uId) AS esPropio
                FROM comentarios c 
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                INNER JOIN paises p ON u.pais = p.id_pais
                WHERE c.$tipoId = :lpId
                ORDER BY c.fecha DESC
            ");
            $cons->execute([
                ':uId' => $idUsuario,
                ':lpId' => $idLibroPelicula
            ]);
                
            $datosComentario = $cons->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success", 
                "resultado" => "agregado",
                "comentario" => $datosComentario
            ]);
            exit;

        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => "Error al guardar"]);
            exit;
        }
    }

    public static function guardarEdicionComentario() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        $input = json_decode(file_get_contents("php://input"), true);
        $idComentario = $input['id_comentario'] ?? null;
        $nuevoTexto = $input['texto'] ?? null;

        $nuevoTexto = htmlspecialchars(trim($input['texto'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (strlen($nuevoTexto) < 2 || strlen($nuevoTexto) > 2000) {
            echo json_encode(["status" => "error", "mensaje" => "Comentario inválido (longitud)"]);
            exit;
        }

        if (!$idUsuario || !$idComentario || !$nuevoTexto) {
            echo json_encode(["status" => "error", "mensaje" => "Datos insuficientes"]);
            exit;
        }

        $pdo = Database::getConnection();
        try {
            // SEGURIDAD: Solo editamos si el ID del comentario coincide con el usuario logueado
            $sql = "UPDATE comentarios SET texto = ?, fecha = NOW() WHERE id = ? AND usuario_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nuevoTexto, $idComentario, $idUsuario]);

            echo json_encode(["status" => "success", "nuevoTexto" => $nuevoTexto]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
            exit;
        }
    }

    function eliminarComentario(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        if (!$idUsuario) {
            echo json_encode(["status" => "error", "mensaje" => "Debes iniciar sesión"]);
            exit;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $id_comentario = $input['id_comentario'] ?? null;

        if (!$id_comentario) {
            echo json_encode(["status" => "error", "mensaje" => "ID de comentario faltante"]);
            exit;
        }

        $pdo = Database::getConnection();

        try {
            $eliminarComentario = "DELETE FROM comentarios WHERE id = ? AND usuario_id = ?";
            $stmt = $pdo->prepare($eliminarComentario);
            $stmt->execute([$id_comentario, $idUsuario]);

            echo json_encode([
                "status" => "success", 
                "resultado" => "eliminado"
            ]);
            exit;
        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => "Error al eliminar comentario: " . $e->getMessage()]);
        }

        exit;
    }

    public static function topTresLibros() {

        $pdo = Database::getConnection();
        
        try {
            // Obtenemos el promedio de estrellas y el total de votos por cada libro
            $sql = "SELECT l.id, l.titulo, l.imagen_url, 
                        AVG(c.puntuacion) as promedio, 
                        COUNT(c.puntuacion) as total_votos
                    FROM calificaciones c
                    INNER JOIN libros l ON c.id_libro = l.id
                    WHERE c.id_libro IS NOT NULL
                    GROUP BY l.id, l.titulo, l.imagen_url
                    ORDER BY promedio DESC, total_votos DESC 
                    LIMIT 3";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $topLibros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "libros" => $topLibros
            ]);
            exit;
        } catch(PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
            exit;
        }
    }
}


?>