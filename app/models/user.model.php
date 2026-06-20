<?php
require_once __DIR__ . '/model.php';

class UserModel extends Model {

    // 1. Obtener un usuario por su nombre de usuario
    public function getByUser($username) {
        try {
            // Preparo la sentencia SQL indicando que debe buscar por una nombre
            // la ejecuto y devuelo lo que me dio
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
            $stmt->execute([$username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }

    // 2. Crear un nuevo usuario
    public function create($username, $passwordHash, $admin = 0) {
        try {
            // Preparo una sentencia SQL para crear una usuario a partir de los datos que me dan
            // la ejecuto y devuelvo la ultima id que se inserto 
            $stmt = $this->db->prepare("INSERT INTO usuarios (usuario, contrasena, admin) VALUES (?, ?, ?)");
            $stmt->execute([$username, $passwordHash, $admin]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }
}