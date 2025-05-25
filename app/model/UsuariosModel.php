<?php

require_once __DIR__ . "/../../config/dbConnection.php";

class UsuariosModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAllUsuarios()
    {
        try {
            // Consulta SQL para obtener todos los usuarios - usar la tabla real de tu base de datos
            // Depuración para mostrar la consulta en el log
            $query = "SELECT ID_U, nombre, apellidos, correo, esAdmin FROM usuarios";
            error_log("Consulta SQL: " . $query);

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Usuarios encontrados: " . count($usuarios));
            if (!empty($usuarios)) {
                error_log("Primer usuario: " . print_r($usuarios[0], true));
            } else {
                error_log("No se encontraron usuarios en la base de datos");
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error en getAllUsuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un usuario por su ID
     * 
     * @param int $idUsuario ID del usuario
     * @return mixed Array con la información del usuario o false si no existe
     */
    public function getUsuarioById($idUsuario)
    {
        try {
            $query = "SELECT ID_U, nombre, apellidos, correo, esAdmin FROM usuarios WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                error_log("Usuario encontrado: " . print_r($usuario, true));
            } else {
                error_log("No se encontró usuario con ID " . $idUsuario);
            }

            return $usuario ?: false;
        } catch (PDOException $e) {
            error_log("Error obteniendo usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar la información de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $usuario Datos del usuario
     * @return bool Resultado de la operación
     */
    public function updateUsuario($idUsuario, $usuario)
    {
        try {
            $query = "UPDATE usuarios SET 
                      nombre = :nombre, 
                      apellidos = :apellidos, 
                      correo = :email, 
                      esAdmin = :admin
                      WHERE ID_U = :idUsuario";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":nombre", $usuario['nombre']);
            $stmt->bindParam(":apellidos", $usuario['apellidos']);
            $stmt->bindParam(":email", $usuario['email']);
            $stmt->bindParam(":admin", $usuario['admin'], PDO::PARAM_INT);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            $result = $stmt->execute();
            error_log("Actualización de usuario ID $idUsuario: " . ($result ? 'éxito' : 'error'));

            return $result;
        } catch (PDOException $e) {
            error_log("Error actualizando usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return bool Resultado de la operación
     */
    public function deleteUsuario($idUsuario)
    {
        try {
            $query = "DELETE FROM usuarios WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $result = $stmt->execute();
            error_log("Eliminación de usuario ID $idUsuario: " . ($result ? 'éxito' : 'error'));

            return $result;
        } catch (PDOException $e) {
            error_log("Error eliminando usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un email ya existe en la base de datos
     * 
     * @param string $email Email del usuario
     * @return bool True si existe, false si no
     */
    public function emailExiste($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM usuarios WHERE correo = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $existe = $stmt->fetchColumn() > 0;
            error_log("Verificación de email $email: " . ($existe ? 'existe' : 'no existe'));

            return $existe;
        } catch (PDOException $e) {
            error_log("Error al verificar si el email existe: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo usuario
     * 
     * @param string $nombre Nombre del usuario
     * @param string $apellidos Apellidos del usuario
     * @param string $email Correo electrónico
     * @param string $password Contraseña (sin hash)
     * @return array Resultado de la operación
     */
    public function crearUsuario($nombre, $apellidos, $email, $password)
    {
        try {
            // Aplicar hash a la contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO usuarios (nombre, apellidos, correo, contraseña, esAdmin) 
                      VALUES (:nombre, :apellidos, :email, :password, 0)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellidos", $apellidos);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $passwordHash);

            $result = $stmt->execute();

            if ($result) {
                return [
                    'success' => true,
                    'userId' => $this->conn->lastInsertId(),
                    'message' => 'Usuario creado con éxito'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear el usuario'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar login de usuario
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña sin hash
     * @return array Resultado de la verificación
     */
    public function verificarLogin($email, $password)
    {
        try {
            $query = "SELECT * FROM usuarios WHERE correo = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar la contraseña
                if (password_verify($password, $usuario['contraseña'])) {
                    return [
                        'success' => true,
                        'usuario' => $usuario,
                        'message' => 'Login correcto'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Contraseña incorrecta'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Email no registrado'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al verificar login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al intentar iniciar sesión'
            ];
        }
    }

    /**
     * Actualizar datos del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $datos Datos a actualizar
     * @return array Resultado de la operación
     */
    public function actualizarUsuario($idUsuario, $datos)
    {
        try {
            $query = "UPDATE usuarios SET ";
            $updateFields = [];
            $params = [":idUsuario" => $idUsuario];

            // Construir la consulta dinámicamente según los campos proporcionados
            foreach ($datos as $campo => $valor) {
                // Sólo permitir actualizar ciertos campos
                if (in_array($campo, ['nombre', 'apellidos', 'correo'])) {
                    $updateFields[] = "$campo = :$campo";
                    $params[":$campo"] = $valor;
                }
            }

            // Si no hay campos para actualizar, retornar error
            if (empty($updateFields)) {
                return [
                    'success' => false,
                    'message' => 'No se proporcionaron datos válidos para actualizar'
                ];
            }

            $query .= implode(", ", $updateFields) . " WHERE ID_U = :idUsuario";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $result = $stmt->execute();

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Datos actualizados correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar los datos'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor al actualizar los datos'
            ];
        }
    }

    /**
     * Verificar la contraseña de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $password Contraseña a verificar
     * @return array Resultado de la verificación
     */
    public function verificarPassword($idUsuario, $password)
    {
        try {
            $query = "SELECT contraseña FROM usuarios WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['contraseña'])) {
                    return [
                        'success' => true,
                        'message' => 'Contraseña correcta'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Contraseña incorrecta'
                    ];
                }
            }
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        } catch (PDOException $e) {
            error_log("Error verificando contraseña: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al verificar la contraseña'
            ];
        }
    }

    /**
     * Cambiar la contraseña de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $password Nueva contraseña (sin hash)
     * @return array Resultado de la operación
     */
    public function cambiarPassword($idUsuario, $password)
    {
        try {
            // Aplicar hash a la nueva contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $query = "UPDATE usuarios SET contraseña = :password WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $passwordHash);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Contraseña actualizada correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error cambiando contraseña: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor al cambiar la contraseña'
            ];
        }
    }

    /**
     * Obtener estadísticas de usuarios
     * 
     * @param string $periodo Periodo de tiempo (dia, mes, anio)
     * @return array Datos de estadísticas
     */
    public function getUsuariosEstadisticas($periodo = 'mes')
    {
        try {
            if ($periodo === 'dia') {
                $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m-%d') as periodo, 
                        COUNT(*) as nuevos_usuarios 
                        FROM usuarios 
                        WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                        GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m-%d') 
                        ORDER BY periodo";
            } else if ($periodo === 'mes') {
                $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as periodo, 
                        COUNT(*) as nuevos_usuarios 
                        FROM usuarios 
                        WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                        GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m') 
                        ORDER BY periodo";
            } else {
                $query = "SELECT DATE_FORMAT(fecha_registro, '%Y') as periodo, 
                        COUNT(*) as nuevos_usuarios 
                        FROM usuarios 
                        GROUP BY DATE_FORMAT(fecha_registro, '%Y') 
                        ORDER BY periodo";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas de usuarios: " . $e->getMessage());
            return [];
        }
    }
}
