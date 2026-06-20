<?php
class Model {
    protected $db; 
    private $sqlFilePath;

    public function __construct($config) {
        // se extrea el arreglo db del arreglo de config
        $c = $config['db'];

        // se extrae la ruta del archivo sql del arreglo de config, si no existe se asigna null
        $this->sqlFilePath = $config['deploy']['sql_file'] ?? null;

        // se intenta conectar a la base de datos
        try {
            $this->db = new PDO($c['dsn'], $c['user'], $c['pass'], $c['options']);
            
        }
        // si la base de datos no existe
        catch (PDOException $e) {
            
            // si el error es 1049 (base de datos no encontrada)
            if ($e->getCode() == 1049) {

                // se crea una nueva conexión con el motor de base de datos pero sin especificar la base de datos
                $dsnSinDb = preg_replace('/dbname=[^;]+;?/', '', $c['dsn']);
                $this->db = new PDO($dsnSinDb, $c['user'], $c['pass'], $c['options']);

                // se extrae el nombre de la base de datos del DSN
                preg_match('/dbname=([^;]+)/', $c['dsn'], $matches);
                $dbName = $matches[1] ?? 'local_comida';

                // se crea la base de datos si no existe
                $this->db->exec("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

                // se selecciona la base de datos recién creada
                $this->db->exec("USE `$dbName`");
                
            } else {

                // si el error es diferente, se lanza la excepción
                throw $e;
            }
        }
        
        // se llama al método de despliegue para crear las tablas si la base de datos estaba vacía
        $this->_deploy();
    }

    private function _deploy() {
        // se extraen las tablas de la base de datos
        $query = $this->db->query('SHOW TABLES');
        $tables = $query->fetchAll();
        
        // se comprueba si la base de datos está vacía
        if (count($tables) == 0) {
            
            // si la base de datos está vacía, se ejecuta el archivo SQL para crear las tablas
            if ($this->sqlFilePath && file_exists($this->sqlFilePath)) {
                $sql = file_get_contents($this->sqlFilePath);
                $this->db->exec($sql); 

            }
            
            // si no se encuentra el archivo SQL, se muestra un mensaje de error 
            else {
                die("Error de Auto Deploy: No se encontró el archivo SQL en la ruta especificada.");
            }
        }
    }
}