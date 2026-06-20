<?php

require_once __DIR__ . '/request.php';
require_once __DIR__ . '/response.php';

// Clase paterna del Route y Middleware
abstract class Routable {
    abstract public function run($request, $response);
}

abstract class Middleware extends Routable {
    public function match($url, $verb) {
        // Esta función hace que cuando se pida el match al los middlewares
        // Devuelva verdadero al hacer que los middlewares extienda de esta clase
        return true;
    }

    abstract public function run($request, $response);
}

class Route extends Routable {
    private $url;
    private $verb;
    private $controller;
    private $method;
    private $params;

    public function __construct($url, $verb, $controller, $method){
        $this->url = $url;
        $this->verb = $verb;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = [];
    }

    // Función encargada hacer matchear la URL y el metodo
    // con una de las rutas cargadas por el api-router
    public function match($url, $verb) {
        // Primero comprueba que el verbo sea el mismo si no ya retorna falso 
        if($this->verb != $verb){
            return false;
        }

        // Separa la URL pasada por la funcion match en un arreglo
        $partsURL = explode("/", trim($url,'/'));

        // Separa la URL pasada cuando se creo el route
        $partsRoute = explode("/", trim($this->url,'/'));
        
        // Las compara si tienen el mismo tamaño los arreglos
        // resultantes y si no es asi retorna falso
        if(count($partsRoute) != count($partsURL)){
            return false;
        }

        // Por cada parte del partsRoute va a loopear 
        foreach ($partsRoute as $key => $part) {
            
            // Pregunta si el primer caracter es : para saber si viene una id
            // si no es asi es la id
            if($part[0] != ":"){

                //Se comprueba que la palabra sea la misma si no retorna falso
                if($part != $partsURL[$key])
                return false;
            } 
            else 
            {   
                //En caso de ser la id guarda lo que vino por URL en el parametro
                $this->params[''.substr($part,1)] = $partsURL[$key];
            }
        }
        // Si toda la URL conicide devuelve verdadero
        return true;
    }
    public function run($request, $response){
        $controller = $this->controller;  
        $method = $this->method;
        // Añade la id a la request si es que se necesitaba
        $request->params = (object) $this->params;

        // Construye el new a partir del controlador, metodo y request,
        // con los parametros los parametros pasados por el Router
        (new $controller())->$method($request, $response);
    }
}

class Router {
    private $routeTable = [];
    private $defaultRoute;
    private $request;
    private $response;

    public function __construct() {
        $this->defaultRoute = null;
        $this->request = new Request();
        $this->response = new Response();
    }

    // Esta funcion sirve para hacer coincidir la URL y el metodo pasado
    // con el Arreglo de routeTable
    public function route($url, $verb) {
        // Por cada valor del routeTable va a loopear
        foreach ($this->routeTable as $route) {

            // Se pregunta si match es verdadero ejecutando la función de match
            // del route en caso de ser de un middleware siempre va a ser true 
            if ($route->match($url, $verb)) {

                // Si fue verdadero le pide al route costruir y ejecutar el controller
                // En caso de ser un middleware hara lo que tenga que hacer el middleware
                $route->run($this->request, $this->response);

                // Luego consulta si la response si tiene el valor finished en true
                // usando la funcion hasFinished y si es asi retorna
                if($this->response->hasFinished())
                    return;
            }
        }
        //Si ninguna ruta coincide con el pedido y se configuró ruta por defecto.
        if ($this->defaultRoute != null)
            $this->defaultRoute->run($this->request, $this->response);
    }

    // Añade un middleware al arreglo routeTable
    public function addMiddleware($middleware) {
        $this->routeTable[] = $middleware;
    }
    
    // Añade una ruta al arreglo routeTable
    public function addRoute ($url, $verb, $controller, $method) {
        $this->routeTable[] = new Route($url, $verb, $controller, $method);
    }

    public function setDefaultRoute($controller, $method) {
        $this->defaultRoute = new Route("", "", $controller, $method);
    }
}
