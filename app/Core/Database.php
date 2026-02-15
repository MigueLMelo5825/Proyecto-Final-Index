<?php
class Database {

    private static ? PDO $instance = null;

    public static function getConnection(): PDO { // al ser una funcion statica podemos llamarla como: Database::getConnection().

        if (self::$instance === null) { // si no existe una conexion se crea una, en caso de que si exista trabajamos con la actual

            // Usamos las variables de entorno Docker
            $host = getenv('DB_HOST')     ?: 'db';
            $dbname = getenv('DB_DATABASE') ?: 'indexproyecto';
            $user = getenv('DB_USERNAME')   ?: 'root';
            $pass = getenv('DB_PASSWORD')   ?: 'indexLIM_5825.';

            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Configura PDO para que lance excepciones en caso de errores de SQL
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Hace que los resultados de las consultas se devuelvan siempre como arreglos asociativos por defecto.
                    PDO::ATTR_EMULATE_PREPARES => false, // Desactiva la emulación de consultas preparadas, obligando a usar las reales del motor de la base de datos
                ]
            );
        }  
        
        return self::$instance; //retornamos la conexion
    }
}