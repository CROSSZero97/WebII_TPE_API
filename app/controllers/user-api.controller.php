<?php
require_once __DIR__ . '/../models/user.model.php';
require_once __DIR__ . '/../../libs/jwt/jwt.php';

class UserApiController {
    private $model;

    public function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $this->model = new UserModel($config);
    }

    // GET: /token
    public function getToken($req, $res) {
        //Extrae la autorizacion del y la separa en un array
        $auth_header = $req->authorization;
        $auth_header = explode(' ', $auth_header);

        // Comprueba que el array sea distinto de 2
        // y que el primer valor del arreglo sea Basic si no se retorna un error
        if (count($auth_header) != 2 || $auth_header[0] != 'Basic') {
            return $res->json("Error de autenticación. Utilice Basic Auth.", 401);
        }

        // Luego descodifica el segundo valor de array
        // Y lo separa en 2 partes user y contraseña
        $user_pass = base64_decode($auth_header[1]);
        $user_pass = explode(':', $user_pass);
        
        // Comprueba que el array sea distinto de 2 si no se retorna un error
        if (count($user_pass) !== 2) {
            return $res->json("Credenciales mal formadas", 400);
        }

        // Luego define el user y la contraseña como lo explique 
        $user = $user_pass[0];
        $pass = $user_pass[1];

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Extrae el usuario de la base de datos segun su usarname 
            $dbUser = $this->model->getByUser($user);

            // Luego hace las comprobaciones que el usuario no este vacio
            // y que la contraseña coincida
            if (!empty($dbUser) && password_verify($pass, $dbUser['contrasena'])) {
                // Si es el caso se genera un Tokne JWT con duracion de 1 hora
                $token = createJWT(array(
                    'sub' => $dbUser['id'],
                    'user' => $dbUser['usuario'],
                    'role' => $dbUser['admin'] == 1 ? 'admin' : 'user',
                    'exp' => time() + 3600 // 1 hora de validez
                ));
                // Y retorno la respuesta con el token JWT al JSON de la Response
                return $res->json(['token' => $token], 200);
            } else {
                // Si no se devuelve que el usuario o contraseña incorrectos
                return $res->json("Usuario o contraseña incorrectos", 401);
            }
        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno del servidor.", 500);
        }
    }
    
    // POST: /usuarios
    public function register($req, $res) {
        // Extrae el username y la contraseña del body
        $username = $req->body->usuario ?? null;
        $password = $req->body->contrasena ?? null;

        // Comprueba que no esten vacias si no se retorna un error
        if (empty($username) || empty($password)) {
            return $res->json("Faltan datos obligatorios (usuario y contrasena)", 400);
        }

        // Sanitiza el nombre del usuario
        $usernameClean = filter_var(trim($username), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Comprueba que la contraseña tenga mas de 3 valores si no se retorna un error
        if (strlen($password) < 4) {
            return $res->json("La contraseña debe contener al menos 4 caracteres.", 400);
        }

        // Este try y catch esta simplemente por si sucede algo con la base de datos
        // Pueda igualmente reenviar un error amigable para que se entienda que paso
        // Se puede quitar no hay problema no cambia en mucho realmente
        try {
            // Extrae el usuario de la base de datos segun su usarname 
            $existing = $this->model->getByUser($usernameClean);

            // Compruba que no exista ese usuario si no se retorna un error
            if (!empty($existing)) {
                return $res->json("El nombre de usuario ya está en uso.", 400);
            }

            // Si no existe el usuario se hashea la contraseña y se crea el nuevo usuario
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $newId = $this->model->create($usernameClean, $passwordHash, 0); 

            // Y retorno la respuesta con el usuario junto a su id
            // y si es admin al JSON de la Response
            return $res->json([
                "message" => "Usuario registrado correctamente",
                "user" => [
                    "id" => (int)$newId,
                    "usuario" => $usernameClean,
                    "admin" => 0
                ]
            ], 201);

        } catch (Exception $e) {
            // y si la base de datos dio algun error lanzo este mensaje
            return $res->json("Error interno del servidor.", 500);
        }
    }
}