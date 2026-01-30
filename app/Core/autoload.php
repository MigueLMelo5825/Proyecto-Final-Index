<?php
/**
 * Mapea el nombre de la clase a su archivo correspondiente 
 * dentro de la estructura de la carpeta 'app/'.
 * * NOTA: Este autoloader asume que:
 * 1. El nombre del archivo coincide con el nombre de la clase (ej. 'Libro' -> 'Libro.php').
 */

// Define la ruta base de la aplicación.
// __DIR__ es la carpeta donde está este archivo (la raíz del proyecto).
$appBaseDir = __DIR__ . '/../'; 

// Define las subcarpetas dentro de 'app/' que contienen las clases.
// El orden no importa, pero es bueno ser explícito.
const CLASS_PATHS = [
    'Core/',         // Para Database.php
    'Models/',       // Para Libro.php, Usuario.php
    'Controllers/',  // Para Controller.php, LibroController.php, UsuarioController.php
];

/**
 * Función de autocarga que se registra en el sistema PHP.
 * @param string $className El nombre de la clase que PHP necesita cargar (ej. 'LibroController').
 */
function Autoloader($className) {
    global $appBaseDir; // Accede a la ruta base definida fuera de la función.
    
    // Intenta encontrar la clase en cada una de las rutas definidas
    foreach (CLASS_PATHS as $path) {
        
        // Construye la ruta completa del archivo esperado
        $file = $appBaseDir . $path . $className . '.php';

        // Comprueba si el archivo existe
        if (file_exists($file)) {
            // Si el archivo existe, lo incluye y termina la búsqueda.
            require_once $file;
            return; 
        }
    }
    
    // Si no se encuentra la clase, lanza un error.
}

// 4. Registra la función de autocarga en el sistema PHP.
spl_autoload_register('Autoloader');

