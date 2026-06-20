<?php
require_once __DIR__ . '/model.php';

class LocalModel extends Model {

    public function getById($id) {
        // Preparo la sentencia SQL indicando que debe buscar por una id
        // la ejecuto y devuelo lo que me dio, si no me dio nada devuelvo null
        $stmt = $this->db->prepare("SELECT * FROM clocal WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
}