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
                $nuevoPromedio = round($cons->fetchColumn(), 1);

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
        $comentario = $input['texto'] ?? null; //comentario registrado por el usuario

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
                    VALUES (:usuario_id, :idLibroPeliula, :comentario, NOW())
                    ON DUPLICATE KEY UPDATE texto = :nuevo_comentario, fecha = NOW()";
            
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([
                ':usuario_id'    => $idUsuario,
                ':idLibroPeliula' => $idLibroPelicula,
                ':comentario'    => $comentario,
                ':nuevo_comentario' => $comentario
            ]);

            // OBTENEMOS LOS DATOS DEL USUARIO (para mostrar el nombre en el feed dinámico)
            $cons = $pdo->prepare("
                SELECT c.texto, c.fecha, u.username, u.foto, u.pais
                FROM comentarios c 
                LEFT JOIN usuarios u ON c.usuario_id = u.id 
                WHERE c.usuario_id = :uId AND c.$tipoId = :lpId
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

    function eliminarComentario(){
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

        if (!$idLibroPelicula || !$tipo) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            exit;
        }

        $pdo = Database::getConnection();

        try{
            // Definimos qué columna usar según el tipo
            $tipoId = ($tipo === 'libro') ? 'id_libro' : 'id_pelicula';

            $eliminarComentario = "DELETE FROM comentarios WHERE usuario_id = ? AND $tipoId = ?";
            
                                
            $stmt = $pdo->prepare($eliminarComentario);
            $stmt->execute([
                $idUsuario,
                $idLibroPelicula,
            ]);
            $resultado = "eliminado";

            echo json_encode([
                    "status" => "success", 
                    "resultado" => $resultado, // "agregado"
                ]);
                exit;

        }catch(PDOException $e){
            error_log($e->getMessage());
        }
    }
}


?>