<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

set_exception_handler(function (Throwable $e){
    http_response_code(500);
    echo json_encode([
        'error' => 'Une erreur est survenue',
        'code' => $e->getCode()
    ]);
});

$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

$dsn = "mysql:host=" . $_ENV['DB_HOST'] . 
    ";port=" . $_ENV['DB_PORT'] . 
    ";dbname=" . $_ENV['DB_NAME'] .
    ";charset=" . $_ENV['DB_CHARSET'];

$pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);

var_dump($pdo);


