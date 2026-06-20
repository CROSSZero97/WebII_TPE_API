<?php
class Request {
    public $body = null; # { nombre: 'Saludar', descripcion: 'Saludar a todos' }
    public $params = null; # /api/tareas/:id
    public $query = null; # ?soloFinalizadas=true
    public $user = null; # Información del usuario autenticado
    public $authorization = null;

    public function __construct() {
        // Se extrae los valores del body si no se carga nulo
        try {
            $this->body = json_decode(file_get_contents('php://input'));
        }
        catch (Exception $e) {
            $this->body = null;
        }
        // Extrae la autorizacion y si no la deja en vacio
        $this->authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            
        // Extrae las parametros del metodo GET como ?page ?limit ?sort
        // y lo cuarda como objetos osea
        // page = 1
        // limit = 3 
        // sort = precio
        $this->query = (object) $_GET;
    }
}