<?php
// ESTO SUBE UN NIVEL: de /app a /
require_once __DIR__ . "/../config/conexion.php";
// Encriptar está dentro de /app/models
require_once __DIR__ . "/models/encriptarModel.php";

try {
    $pdo = Conexion::conectar();

    // DATOS DE PRUEBA
    $nombre       = "Administrador";
    $correoPlano  = "admn@admin.com";
    $passPlana    = "123456788";
    $telefono     = "77777777";
    $especialidad = "General";
    $es_admin     = 1;
    $estado       = 1;

    // ENCRIPTAR IGUAL QUE EN EL LOGIN
    $correoEncriptado      = Encriptar::openCypher("encrypt", $correoPlano);
    $passEncriptada        = Encriptar::openCypher("encrypt", $passPlana);

    $sql = "INSERT INTO odontologo
                (nombre, correo, contrasenia, telefono, especialidad, es_admin, estado)
            VALUES
                (:nombre, :correo, :contrasenia, :telefono, :especialidad, :es_admin, :estado)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nombre"       => $nombre,
        ":correo"       => $correoEncriptado,
        ":contrasenia"  => $passEncriptada,
        ":telefono"     => $telefono,
        ":especialidad" => $especialidad,
        ":es_admin"     => $es_admin,
        ":estado"       => $estado,
    ]);

    echo "Usuario de prueba creado correctamente";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
