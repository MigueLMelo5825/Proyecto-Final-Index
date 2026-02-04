<?php

class Conexion {

    private static $conexion = null;

    public static function getConexion() {

        if (self::$conexion === null) {

            $host = "localhost";
            $dbname = "indexproyecto";   // Cambia si tu BD tiene otro nombre
            $user = "root";
            $pass = "";

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
