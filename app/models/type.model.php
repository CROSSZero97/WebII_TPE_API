<?php
require_once __DIR__ . '/model.php';

class TypeModel extends Model {

    public function getById($id) {
        // Preparo la sentencia SQL indicando que debe buscar por una id
        // la ejecuto y devuelo lo que me dio, si no me dio nada devuelvo null
        $stmt = $this->db->prepare("SELECT * FROM tipos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

}