<?php

class Conexion {

    private static $conexion = null;

    public static function getConexion() {

        if (self::$conexion === null) {

            $host = getenv('DB_HOST') ?: 'db';
            $dbname = getenv('DB_DATABASE') ?: 'indexproyecto';
            $user = getenv('DB_USERNAME') ?: 'root';
            $pass = getenv('DB_PASSWORD') ?: 'indexLIM_5825.';

            try {

                self::$conexion = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $user,
                    $pass
                );

                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }

        return self::$conexion;
    }
}
