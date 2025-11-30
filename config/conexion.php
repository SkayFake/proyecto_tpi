<?php

class Conexion {
    private static ?PDO $conexion = null;
    private function __construct() {}

    public static function conectar(): ?PDO
    {
        if (self::$conexion === null) {

            // Tomar valores desde variables de entorno (Render)
            $host     = getenv("DB_HOST");
            $dbname   = getenv("DB_NAME");
            $user     = getenv("DB_USER");
            $password = getenv("DB_PASS");
            $port     = getenv("DB_PORT");

            // Construir cadena de conexión correcta
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];

            try {
                self::$conexion = new PDO($dsn, $user, $password, $opciones);
            } catch (PDOException $e) {

                // Mostrar error exacto en logs de Render
                error_log("Error de conexión MySQL: " . $e->getMessage());

                // Mensaje de salida seguro
                die("❌ Error de conexión a la base de datos.");
            }
        }

        return self::$conexion;
    }
}
