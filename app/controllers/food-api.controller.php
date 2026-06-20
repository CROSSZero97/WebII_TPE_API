<?php
require_once __DIR__ . '/../models/food.model.php';
require_once __DIR__ . '/../models/type.model.php';
require_once __DIR__ . '/../models/local.model.php';

class FoodApiController {
    private $model;
    private $typeModel;
    private $localModel;

    public function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $this->model = new FoodModel($config);
        $this->typeModel = new TypeModel($config);
        $this->localModel = new LocalModel($config);
    }

    // GET: /comidas
    public function getApiFoods($req, $res) {
        // Extraigo los datos de la request para que la modelo sepa si tiene que hacer
        // mas operaciones o simplemente extraer toda la tabla de comidas completa
        $filtroTipo = $req->query->tipo ?? null;
        $sort = $req->query->sort ?? null;
        $order = $req->query->order ?? 'ASC';
        
        // Luego extraigo los valores para la paginacion si es que hay si no se setean como nulos
        $page = isset($req->query->page) ? (int)$req->query->page : null;
        $limit = isset($req->query->limit) ? (int)$req->query->limit : null;

        // Comprueba que el filtro no sea nulo
        if ($filtroTipo !== null) {
            // Si no es le pide al Type Model que lo busque
            // si no devuelve nada es falso la existencia y se retorna un error
            if (!$this->typeModel->getById($filtroTipo)) {
                return $res->json("El tipo de comida indicado para filtrar no existe.", 404);
            }
        }

        // Comprueba que el sort no sea nulo
        if ($sort !== null) {
            // Genero un arreglo temporal con los posibles ordenamientos
            // y pregunto si no existe el sort en el arreglo si es asi se retorna un error
            $allowedSorts = ['id', 'nombre', 'img', 'descripcion', 'tipo', 'precio', 'clocal'];
            if (!in_array(strtolower($sort), $allowedSorts)) {
                return $res->json("El parámetro de ordenamiento '$sort' no es válido.", 400);
            }
        }

        // Compruebo que el order concide con ASC o DESC y si no se retorna un error
        $order = strtoupper($order);
        if ($order !== 'ASC' && $order !== 'DESC') {
            return $res->json("El parámetro 'order' solo acepta 'ASC' o 'DESC'.", 400);
        }

        // Compruebo de que alguno de los dos valores en caso de la paginacion no sea nulo
        if ($page !== null || $limit !== null) {
            // Compruebo que mando los dos datos 
            // Si mandó uno se retorna un error
            if ($page === null || $limit === null) {
                return $res->json("Para paginar, debe enviar tanto 'page' como 'limit'.", 400);
            }
            // Compruebo que los valores no sean menor a 0
            // Si alguno de los dos valores lo es, se retorna un error
            if ($page <= 0 || $limit <= 0) {
                return $res->json("Los parámetros 'page' y 'limit' deben ser mayores a cero.", 400);
            }
        }

        // Y luego seteo el offset, osea cuantas filas  se va a saltear
        // la base de datos antes de empezar a traer filas solicitadas
        $offset = ($page !== null && $limit !== null) ? ($page - 1) * $limit : null;

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Le paso a la valores al modelo para que sepa que traeme y retorno lo que me trajo
            $foods = $this->model->getApiFoods($filtroTipo, $sort, $order, $limit, $offset);
            return $res->json($foods, 200);
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno al obtener las comidas.", 500);
        }
    }

    // GET: /comidas/:id
    public function getById($req, $res) {
        // Extrae la id de la comida a buscar del parametro de la request
        $id = $req->params->id;

        // Es una comprobacion simple de que el id sea verdadero y sea un numero
        // para evitar que se mande una letra e igual pase
        if (!is_numeric($id) || $id <= 0) {
            return $res->json("El ID proporcionado no es válido. Debe ser un número entero.", 400);
        }

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Le paso la id de la comida al modelo para que intente encontrarla 
            $food = $this->model->getById($id);
            
            // Se comprueba si el modelo retorno vacio y si es asi se retorna un error
            if (!$food) {
                return $res->json("La comida con el id=$id no existe", 404);
            }

            // Si no retorno vacio devuelve la comida al JSON de la Response
            return $res->json($food, 200);
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno al obtener la comida.", 500);
        }
    }

    // POST: /comidas
    public function insert($req, $res) {
        // Extrae las variables para crear una de la comida del body de la request
        // si no vino nada los seteo como nullos o vacios dependiendo el caso
        $nombre = $req->body->nombre ?? null;
        $img = $req->body->img ?? '';
        $descripcion = $req->body->descripcion ?? '';
        $tipo = $req->body->tipo ?? null;
        $precio = $req->body->precio ?? null;
        $clocal = $req->body->clocal ?? null;

        // Comprueba que las varibles necesarias no esten vacios
        // si alguno lo esta se retorna un error
        if (empty($nombre) || empty($tipo) || empty($precio) || empty($clocal)) {
            return $res->json("Faltan datos obligatorios (nombre, tipo, precio, clocal)", 400);
        }

        // Se comprueba que el tipo de comida exista si no se retorna un error
        if (!$this->typeModel->getById($tipo)) {
            return $res->json("El tipo de comida indicado no existe.", 404);
        }
        // Se comprueba que el local exista si no se retorna un error
        if (!$this->localModel->getById($clocal)) {
            return $res->json("El local indicado no existe.", 404);
        }

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Se crea la nueva comida pasandole los valores necesarios
            // luego se extrae y se retorna un mensaje al JSON de la Response 
            $id = $this->model->insert($nombre, $img, $descripcion, $tipo, $precio, $clocal);
            $food = $this->model->getById($id);
            return $res->json($food, 201);
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno del servidor.", 500);
        }
    }

    // PUT: /comidas/:id
    public function update($req, $res) {
        // Extrae la id de la comida a buscar del parametro de la request
        // y se extrae la comida segun la id pasada 
        $id = $req->params->id;

        // Es una comprobacion simple de que el id sea verdadero y sea un numero
        // para evitar que se mande una letra e igual pase
        if (!is_numeric($id) || $id <= 0) {
            return $res->json("El ID proporcionado no es válido. Debe ser un número entero.", 400);
        }

        $food = $this->model->getById($id);
        
        // Si la comida no existe se retorna un error
        if (!$food) {
            return $res->json("La comida con el id=$id no existe", 404);
        }
        // Extrae las variables para modifcar una de la comida del body de la request
        // si no vino nada los seteo como nullos o vacios dependiendo el caso
        $nombre = $req->body->nombre ?? null;
        $img = $req->body->img ?? '';
        $descripcion = $req->body->descripcion ?? '';
        $tipo = $req->body->tipo ?? null;
        $precio = $req->body->precio ?? null;
        $clocal = $req->body->clocal ?? null;

        // Comprueba que las varibles necesarias no esten vacios
        // si alguno lo esta se retorna un error
        if (empty($nombre) || empty($tipo) || empty($precio) || empty($clocal)) {
            return $res->json("Faltan datos obligatorios", 400);
        }
        // Se comprueba que el tipo de comida exista si no se retorna un error
        if (!$this->typeModel->getById($tipo)) {
            return $res->json("El nuevo tipo de comida indicado no existe.", 404);
        }
        // Se comprueba que el local exista si no se retorna un error
        if (!$this->localModel->getById($clocal)) {
            return $res->json("El nuevo local indicado no existe.", 404);
        }

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Se actualiza la comida de la id indicada pasandole los valores necesarios
            // luego se extrae y se retorna un mensaje al JSON de la Response 
            $this->model->update($id, $nombre, $img, $descripcion, $tipo, $precio, $clocal);
            $foodUpdated = $this->model->getById($id);
            return $res->json($foodUpdated, 200);
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno del servidor.", 500);
        }
    }

    // DELETE: /comidas/:id
    public function delete($req, $res) {
        // Extrae la id de la comida a buscar del parametro de la request 
        $id = $req->params->id;

        // Es una comprobacion simple de que el id sea verdadero y sea un numero
        // para evitar que se mande una letra e igual pase
        if (!is_numeric($id) || $id <= 0) {
            return $res->json("El ID proporcionado no es válido. Debe ser un número entero.", 400);
        }

        // y se extrae la comida segun la id pasada
        $food = $this->model->getById($id);

        // Si la comida no existe se retorna un error
        if (!$food) {
            return $res->json("La comida con el id=$id no existe", 404);
        }
        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Se elimina la comida de la id indicada 
            // y se retorna un mensaje al JSON de la Response 
            $this->model->delete($id);
            return $res->json("La comida con el id=$id se eliminó correctamente", 200);
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno del servidor.", 500);
        }
    }
}