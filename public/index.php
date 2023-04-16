<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\DbInitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Crud\Exception\UnprocessableContentException;
use App\Crud\meublesCrud;
use Symfony\Component\Dotenv\Dotenv;

header('content-type: application/json; charset=UTF-8');


$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

ExceptionHandlerInitializer::registerGlobalExceptionHandler();
$pdo = DbInitializer::getPdoInstance();

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];
const RESOURCES = ['meubles'];

$uriParts = explode('/', $uri);
$isItemOperation = count($uriParts) === 3;
$productsCrud = new meublesCrud($pdo);

// Collection de produits
if ($uri === '/products' && $httpMethod === 'GET') {
  echo json_encode($meublesCrud->findAll());
  exit;
}


if ($uri === '/meubles' && $httpMethod === 'POST') {
  try {
    $data = json_decode(file_get_contents('php://input'), true);
    $meubleId = $meublesCrud->create($data);
    http_response_code(201);
    echo json_encode([
      'uri' => '/meubles/' . $meubleId
    ]);
  } catch (UnprocessableContentException $e) {
    http_response_code(422);
    echo json_encode([
      'error' => $e->getMessage()
    ]);
  } finally {
    exit;
  }
}


if (!$isItemOperation) {
  http_response_code(404);
  echo json_encode([
    'error' => 'Route non trouvée'
  ]);
  exit;
}


$resourceName = $uriParts[1];
$id = intval($uriParts[2]);
if ($id === 0) {
  http_response_code(400);
  echo json_encode([
    'error' => 'ID non valide'
  ]);
  exit;
}





