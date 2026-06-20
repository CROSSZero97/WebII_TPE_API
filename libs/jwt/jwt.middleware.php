<?php
require_once __DIR__ . '/jwt.php';

// Extiende la funcion match de la clase Middleware 
// que esta en el router.php Línea 11 a 19

class JWTMiddleware extends Middleware {
    public function run($request, $response) {
        // Extrae de la request los datos de la autorización
        $auth_header = $request->authorization; 
        
        // Comprueba que no este vacio si no se retorna un error
        if (empty($auth_header)) {
            return $response->json("No autorizado. Token ausente.", 401);
        }

        // Separa la autenticacion en un array 
        $auth_header = explode(' ', $auth_header);
        
        // Comprueba que el arreglo de la autenticacion tenga 2 valores Bearer y Token
        // y que el primer valor del arreglo sea Bearer si no se retorna un error
        if (count($auth_header) != 2 || $auth_header[0] != 'Bearer') {
            return $response->json("No autorizado. Formato de token inválido.", 401);
        }
        
        // Carga el token en una variable y se pide al JWT que devuelva un usuario
        // Validandolo con la funcion JWT
        $jwt = $auth_header[1];
        $usuarioDecodificado = validateJWT($jwt);

        // Si no se retorno un usuario o el usuario no tiene rol admin se retorna un error
        if (!$usuarioDecodificado || $usuarioDecodificado->role !== 'admin') {
            return $response->json("Acceso denegado. Se requieren permisos de administrador.", 403);
        }

        // Si paso todas las comprobaciones se guarda el user en la request
        $request->user = $usuarioDecodificado;
    }
}