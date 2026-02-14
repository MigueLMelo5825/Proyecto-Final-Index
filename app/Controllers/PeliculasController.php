<?php


class PeliculasController {

    public function cargarPeliculas() {
        $pdo = Database::getConnection();

        $resultado = ConexionPeliculasApi::importarPeliculas($pdo, 500,"popular");
    
        $mensaje = $resultado
            ? "Películas importadas correctamente."
            : "Error al importar películas.";

        include dirname(__DIR__).'/templates/CargarPeliculas.php';
    }

    public function mostrarTop() {
        $modeloPeliculas = new Peliculas();
        $peliculas = $modeloPeliculas->obtenerTopPeliculas();

        include dirname(__DIR__).'/templates/perfil.php';
    }

    public static function topTresPeliculas(){
        
        $pdo = Database::getConnection();

        try {
            // 1. Contamos los likes agrupando por el ID de la película
            // 2. Unimos con la tabla 'peliculas' para traer sus datos visuales
            $consultaSql = "SELECT p.id, p.titulo, p.portada, COUNT(l.id_pelicula) as total_likes 
                            FROM likes l
                            INNER JOIN peliculas p ON l.id_pelicula = p.id
                            WHERE l.id_pelicula IS NOT NULL
                            GROUP BY p.id, p.titulo, p.portada
                            ORDER BY total_likes DESC 
                            LIMIT 3";

            $stmt = $pdo->prepare($consultaSql);
            $stmt->execute();
            
            $topPeliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "status" => "success",
                "peliculas" => $topPeliculas // Enviamos el array con los datos reales
            ]);
            exit;

        } catch(PDOException $e) {
            echo json_encode([
                "status" => "error", 
                "mensaje" => "Error al obtener el top 3: " . $e->getMessage()
            ]);
            exit;
        }
    }
}
