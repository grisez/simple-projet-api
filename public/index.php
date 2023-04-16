<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\DbInitializer;
use APP\Config\ExceptionHandlerInitializer;
use Symfony\Component\Dotenv\Dotenv;

header('content-type: application/json; charset=UTF-8');


$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

ExceptionHandlerInitializer::registerGlobalExeptionHandler();
$pdo = DbInitializer::getPdoInstance();

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];

if ($uri === '/meubles'&& $httpMethod === 'GET'){
    $stmt = $pdo->query("SELECT * FROM meubles");
    $meubles = $stmt->fetchAll();
}

if ($uri === '/meubles'&& $httpMethod === 'POST'){

}