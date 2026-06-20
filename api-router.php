<?php
require_once 'libs/router/router.php';
require_once 'app/controllers/food-api.controller.php';
require_once 'app/controllers/user-api.controller.php';
require_once 'libs/jwt/jwt.middleware.php';

$router = new Router();

// Ruta de obtención de token
$router->addRoute('token', 'GET', 'UserApiController', 'getToken');
// Ruta de creación de usuario
$router->addRoute('usuarios', 'POST', 'UserApiController', 'register');

// Ruta de obtención de comidas con o sin filtros
$router->addRoute('comidas', 'GET', 'FoodApiController', 'getApiFoods');
// Ruta de obtención de comida por id
$router->addRoute('comidas/:id', 'GET', 'FoodApiController', 'getById');

// El middleware lo cargo acá, para que de acá en adelante las rutas
// requieran de un Token JWT, si no el middleware rechazara la petición
$router->addMiddleware(new JWTMiddleware());

// Ruta de creación de comidas, requiere JSON
$router->addRoute('comidas', 'POST', 'FoodApiController', 'insert');
// Ruta de edición de comidas por id, requiere JSON
$router->addRoute('comidas/:id', 'PUT', 'FoodApiController', 'update');
// Ruta de eliminación de comidas por id
$router->addRoute('comidas/:id', 'DELETE', 'FoodApiController', 'delete');

// Se ejecuta el route para matchear la URL y el metodo con alguna de las rutas
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);