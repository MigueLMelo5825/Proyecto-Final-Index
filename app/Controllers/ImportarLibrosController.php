<?php

class ImportarLibrosController {

    public function importar() {

        try {
            $pdo = Database::getConnection();

            // Temas que quieres importar
$temas = [

    // FANTASÍA MODERNA
    "fantasy novel 2025",
    "fantasy novel 2024",
    "fantasy novel 2023",
    "epic fantasy 2024",
    "epic fantasy 2023",
    "urban fantasy 2024",
    "urban fantasy 2023",
    "young adult fantasy 2024",
    "YA fantasy 2023",
    "fantasy romance 2024",
    "romantasy 2024",
    "romantasy 2023",

    // ROMANCE ACTUAL
    "romance novel 2025",
    "romance novel 2024",
    "romance novel 2023",
    "romantic fiction 2024",
    "romantic fiction 2023",
    "new adult romance 2024",
    "new adult romance 2023",
    "contemporary romance 2024",
    "romantic suspense 2024",
    "romantic suspense 2023",

    // THRILLER / MISTERIO
    "thriller novel 2025",
    "thriller novel 2024",
    "thriller novel 2023",
    "psychological thriller 2024",
    "psychological thriller 2023",
    "crime thriller 2024",
    "crime thriller 2023",
    "mystery novel 2024",
    "mystery novel 2023",
    "detective novel 2024",
    "detective novel 2023",
    "suspense novel 2024",

    // TERROR
    "horror novel 2025",
    "horror novel 2024",
    "horror novel 2023",
    "supernatural horror 2024",
    "supernatural horror 2023",
    "gothic horror 2024",
    "gothic horror 2023",

    // CIENCIA FICCIÓN
    "science fiction novel 2025",
    "science fiction novel 2024",
    "science fiction novel 2023",
    "dystopian novel 2024",
    "dystopian novel 2023",
    "space opera 2024",
    "space opera 2023",
    "post apocalyptic novel 2024",
    "post apocalyptic novel 2023",

    // JUVENIL / YA
    "young adult fiction 2025",
    "young adult fiction 2024",
    "young adult fiction 2023",
    "YA romance 2024",
    "YA romance 2023",
    "YA thriller 2024",
    "YA thriller 2023",
    "YA fantasy 2025",

    // NOVELA CONTEMPORÁNEA
    "contemporary fiction 2025",
    "contemporary fiction 2024",
    "contemporary fiction 2023",
    "women's fiction 2024",
    "women's fiction 2023",
    "literary fiction 2024",
    "literary fiction 2023",

    // NOVELA GRÁFICA / MANGA
    "graphic novel 2024",
    "graphic novel 2023",
    "manga 2024",
    "manga 2023"
];



            echo "<h2>Importación de Libros</h2>";
            echo "<div style='font-family: sans-serif; padding: 10px; background: #f4f4f4;'>";

            foreach ($temas as $t) {
                $res = ConexionApiLibros::buscarEImportar($t, $pdo);
                echo "<strong>$t:</strong> $res <br>";
                usleep(400000); // evitar saturar la API
            }

            echo "</div>";
            echo "<p style='color: green; font-weight: bold;'>Importación finalizada.</p>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
