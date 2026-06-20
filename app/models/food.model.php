<?php
require_once __DIR__ . '/model.php';

class FoodModel extends Model {

    // Esta funcion es la encagada principal de la paginacion, el filtrado y la el ordenamiento
    public function getApiFoods($filtroTipo = null, $sort = null, $order = 'ASC', $limit = null, $offset = null) {
        try {
            // Preparo los datos y la sentencia SQL para que extraiga todos los datos de la funcion
            $sql = "SELECT * FROM comida";
            $params = [];

            // Consulto si existe un tipo a filtrar si lo hay cargo el filtro en los 
            // parametros de ejecucion y el tipo = ? para inyectarlo en la sentencia
            if ($filtroTipo !== null) {
                $sql .= " WHERE tipo = ?";
                $params[] = $filtroTipo;
            }

            // Consulto si existe un valor de porque debo ordenar no es nulo
            // Si no es asi saco si es DESC o ASC se fija como ASC
            // y añado el ORDER BY valor de porque debo ordenar y el orden
            if ($sort !== null) {
                $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
                $sql .= " ORDER BY $sort $order";
            }

            // Compruebo que el limite y el offset no sean nulo y si lo lo son los añado
            // a la sequencia como " LIMIT " siendo el limite y el " OFFSET " siendo el offset
            if ($limit !== null && $offset !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }

            //luego preparo la sentencia la ejecuto y devuelvo todas las filas que encontro 
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }

    public function getById($id) {
        try {
            // Preparo la sentencia SQL indicando que debe buscar una comida por una id
            // la ejecuto y devuelo lo que me dio, si no me dio nada devuelvo null
            $stmt = $this->db->prepare("SELECT * FROM comida WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }

    public function insert($nombre, $img, $descripcion, $type_id, $precio, $local_id) {
        try {
            // Preparo una sentencia SQL para crear una comida a partir de los datos que me dan
            // la ejecuto y devuelvo la ultima id que se inserto 
            $stmt = $this->db->prepare("INSERT INTO comida (nombre, img, descripcion, tipo, precio, clocal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $img, $descripcion, $type_id, $precio, $local_id]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }

    public function update($id, $nombre, $img, $descripcion, $type_id, $precio, $local_id) {
        try {
            // Preparo una sentencia SQL para modificar  una comida a partir de los datos que me dan y la ejecuto
            $stmt = $this->db->prepare("UPDATE comida SET nombre = ?, img = ?, descripcion = ?, tipo = ?, precio = ?, clocal = ? WHERE id = ?");
            return $stmt->execute([$nombre, $img, $descripcion, $type_id, $precio, $local_id, $id]);
            
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }

    public function delete($id) {
        try {
            // Preparo la sentencia SQL indicando que debe eliminar una comida por una id y la ejecuto
            $stmt = $this->db->prepare("DELETE FROM comida WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // En caso de cualquier error se carga el error para que el controller lo devuelva
            throw new Exception("DB_ERROR");
        }
    }
}