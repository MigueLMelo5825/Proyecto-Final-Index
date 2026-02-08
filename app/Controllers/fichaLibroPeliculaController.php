<?php
class fichaLibroPeliculaController {

    public function ficha() {
        
        require __DIR__ . '/../templates/ficha_Libro_Y_Peliculas.php';
    }

    public static function guardarLikesYCalificacion(){
        
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
}


?>