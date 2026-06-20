<?php
return [
    // Arreglo de datos para inicializar la DB
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=local_comida;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    
    // Arrego con la ubicacion de la DB
    'deploy' => [
        'sql_file' => __DIR__ . '/../database/local_comida.sql'
    ]
];