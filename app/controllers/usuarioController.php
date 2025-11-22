<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/usuarioModel.php";
require_once __DIR__ . "/../models/encriptarModel.php";

$usuarioModel = new Usuario();

$opcion = isset($_GET["opcion"]) ? trim($_GET["opcion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {

        /* ===================== LOGIN ===================== */
        case "ingresar":

            // Acepta tanto: correo/contrasenia  como email/clave
            $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) :
                      (isset($_POST["email"]) ? trim($_POST["email"]) : null);

            $contrasenia = isset($_POST["contrasenia"]) ? trim($_POST["contrasenia"]) :
                           (isset($_POST["clave"]) ? trim($_POST["clave"]) : null);

            if (empty($correo) || empty($contrasenia)) {
                $response = [
                    "status"  => "error",
                    "message" => "Correo y contraseña son obligatorios"
                ];
                break;
            }

            // Encriptar exactamente igual que cuando guardaste en la BD
            $correoEncriptado      = Encriptar::openCypher("encrypt", $correo);
            $contraseniaEncriptada = Encriptar::openCypher("encrypt", $contrasenia);

            // Este método debe hacer algo como:
            // SELECT * FROM usuarios WHERE correo = ? AND contrasenia = ? AND estado = 1
            $usuario = $usuarioModel->getUsuarioLogin($correoEncriptado, $contraseniaEncriptada);

            if ($usuario) {
                session_start();
                $_SESSION["usuario"] = $usuario;

                $response = [
                    "status"  => "success",
                    "usuario" => $usuario
                ];
            } else {
                $response = [
                    "status"  => "error",
                    "message" => "Correo o contraseña incorrectos"
                ];
            }
        break;

        /* ===================== CERRAR SESIÓN ===================== */
        case "cerrar":
            session_start();
            session_destroy();
            $response = [
                "status"  => "success",
                "message" => "Sesión cerrada exitosamente"
            ];
        break;

        default:
            $response = [
                "status"  => "error",
                "message" => "Opción no válida"
            ];
        break;
    }

} catch (Throwable $e) {
    error_log("Error en usuarioController: " . $e->getMessage());
    $response = [
        "status"  => "error",
        "message" => "Ocurrió un error en el sistema"
    ];
}

echo json_encode($response);
