<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/encriptarModel.php";

class Odontologo
{

    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function agregar(
        string $nombre,
        string $correo,
        string $contrasenia,
        ?string $telefono,
        ?string $especialidad,
        int $es_admin,
        int $estado
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $correoEncriptado      = Encriptar::openCypher('encrypt', $correo);
            $contraseniaEncriptada = Encriptar::openCypher('encrypt', $contrasenia);

            $sql = "CALL sp_odontologo_insert(
                    :nombre,
                    :correo,
                    :contrasenia,
                    :telefono,
                    :especialidad,
                    :es_admin,
                    :estado
                )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":nombre",       $nombre,          PDO::PARAM_STR);
            $stmt->bindValue(":correo",       $correoEncriptado, PDO::PARAM_STR);
            $stmt->bindValue(":contrasenia",  $contraseniaEncriptada, PDO::PARAM_STR);
            $stmt->bindValue(":telefono",     $telefono,        $telefono ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":especialidad", $especialidad,    $especialidad ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":es_admin",     $es_admin,        PDO::PARAM_INT);
            $stmt->bindValue(":estado",       $estado,          PDO::PARAM_INT);

            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error registrar odontólogo: " . $e->getMessage());
            return false;
        }
    }


    public function getOdontologos(): array
    {
        try {
            $sql = "SELECT id_odontologo, nombre, correo, telefono, especialidad, es_admin, estado 
                    FROM odontologo";
            $stmt = $this->conexion->query($sql);
            $odontologos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($odontologos as &$odontologo) {
                $odontologo['correo'] = Encriptar::openCypher('decrypt', $odontologo['correo']);
            }

            return $odontologos;
        } catch (Throwable $e) {
            error_log("Error getOdontologos: " . $e->getMessage());
            return [];
        }
    }

    public function getOdontologoById(int $id_odontologo): ?array
    {
        try {
            $sql = "SELECT id_odontologo, nombre, correo, telefono, especialidad, es_admin, estado 
                    FROM odontologo 
                    WHERE id_odontologo = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id", $id_odontologo, PDO::PARAM_INT);
            $stmt->execute();

            $odontologo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($odontologo) {
                $odontologo['correo'] = Encriptar::openCypher('decrypt', $odontologo['correo']);
                return $odontologo;
            }

            return null;
        } catch (Throwable $e) {
            error_log("Error getOdontologoById: " . $e->getMessage());
            return null;
        }
    }

    public function actualizar(
        int $id_odontologo,
        string $nombre,
        string $correo,
        ?string $telefono = null,
        ?string $especialidad = null,
        ?int $es_admin = null,
        ?int $estado = null,
        ?string $contrasenia = null
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $correoEncriptado = Encriptar::openCypher('encrypt', $correo);

            $contraseniaEncriptada = null;
            if ($contrasenia !== null && $contrasenia !== '') {
                $contraseniaEncriptada = Encriptar::openCypher('encrypt', $contrasenia);
            }

            $sql = "CALL sp_odontologo_update(
                    :id_odontologo,
                    :nombre,
                    :correo,
                    :contrasenia,
                    :telefono,
                    :especialidad,
                    :es_admin,
                    :estado
                )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":id_odontologo", $id_odontologo, PDO::PARAM_INT);
            $stmt->bindValue(":nombre",        $nombre,            PDO::PARAM_STR);
            $stmt->bindValue(":correo",        $correoEncriptado,  PDO::PARAM_STR);

            if ($contraseniaEncriptada !== null) {
                $stmt->bindValue(":contrasenia", $contraseniaEncriptada, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(":contrasenia", null, PDO::PARAM_NULL);
            }

            $stmt->bindValue(":telefono",     $telefono,     $telefono ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":especialidad", $especialidad, $especialidad ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":es_admin",     $es_admin,     $es_admin !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(":estado",       $estado,       $estado !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar odontólogo: " . $e->getMessage());
            return false;
        }
    }


    public function actualizarContrasenia(int $id_odontologo, string $nueva_contrasenia): bool
    {
        try {
            $this->conexion->beginTransaction();
            $contraseniaEncriptada = Encriptar::openCypher('encrypt', $nueva_contrasenia);

            $sql = "UPDATE odontologo SET contrasenia = :contrasenia WHERE id_odontologo = :id";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":contrasenia", $contraseniaEncriptada, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id_odontologo, PDO::PARAM_INT);

            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id_odontologo): bool
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_odontologo_delete(:id_odontologo)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":id_odontologo", $id_odontologo, PDO::PARAM_INT);

            $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            error_log("Error eliminar odontólogo: " . $e->getMessage());
            return false;
        }
    }

    public function validarCredenciales(string $correo, string $contrasenia): ?array
    {
        try {
            $correoEncriptado = Encriptar::openCypher('encrypt', $correo);
            $contraseniaEncriptada = Encriptar::openCypher('encrypt', $contrasenia);

            $sql = "SELECT id_odontologo, nombre, correo, contrasenia, telefono, especialidad, es_admin, estado 
                    FROM odontologo 
                    WHERE correo = :correo AND contrasenia = :contrasenia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":correo", $correoEncriptado, PDO::PARAM_STR);
            $stmt->bindValue(":contrasenia", $contraseniaEncriptada, PDO::PARAM_STR);
            $stmt->execute();

            $odontologo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($odontologo) {
                $odontologo['correo'] = Encriptar::openCypher('decrypt', $odontologo['correo']);
                unset($odontologo['contrasenia']);
                return $odontologo;
            }

            return null;
        } catch (Throwable $e) {
            error_log("Error validar credenciales: " . $e->getMessage());
            return null;
        }
    }

    public function existeCorreo(string $correo): bool
    {
        try {
            $correoEncriptado = Encriptar::openCypher('encrypt', $correo);

            $sql = "SELECT COUNT(*) as total FROM odontologo WHERE correo = :correo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(":correo", $correoEncriptado, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (Throwable $e) {
            error_log("Error verificar correo: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado(int $id_odontologo, int $estado): bool
    {
        try {
            $sql = "UPDATE odontologo
                SET estado = :estado
                WHERE id_odontologo = :id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindValue(':id',     $id_odontologo, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (Throwable $e) {
            error_log("Error actualizarEstado: " . $e->getMessage());
            return false;
        }
    }
}
