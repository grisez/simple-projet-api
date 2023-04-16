<?php

namespace App\Crud;

use App\Crud\Exception\UnprocessableContentException;
use PDO;

class MeublesCrud
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        // Vérifie que les données obligatoires sont présentes
        $requiredFields = ['m_nom', 'm_prix'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new UnprocessableContentException("$field est obligatoire");
            }
        }

        $query = "INSERT INTO meubles (m_nom, m_prix, m_description) VALUES (:m_nom, :m_prix, :m_description)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'm_nom' => $data['m_nom'],
            'm_prix' => $data['m_prix'],
            'm_description' => $data['m_description'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM meubles");
        $products = $stmt->fetchAll();

        return ($products === false) ? [] : $products;
    }

    public function find(int $id, string $resourceName, string $isItemOperation, string $httpMethod): ?array
    {
        // Vérifie si c'est une opération sur un élément unique de la ressource "meubles" et si la méthode est "GET"
        if ($resourceName === 'meubles' && $isItemOperation && $httpMethod === 'GET') {
            $query = "SELECT * FROM meubles WHERE m_id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $id]);

            $product = $stmt->fetch();

            if ($product === false) {
                http_response_code(404);
                echo json_encode([
                    'error' => 'Produit non trouvé'
                ]);
                exit;
            }

            return $product;
        }

        return null;
    }

public function update(int $id, array $data, string $resourceName, string $isItemOperation, string $httpMethod): bool
{
if ($resourceName === 'meubles' && $isItemOperation && $httpMethod === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['m_nom']) || !isset($data['m_prix'])) {
    http_response_code(422);
    echo json_encode([
        'error' => 'nom et prix obligatoire'
    ]);
    exit;
    }

    $query = "UPDATE meubles SET m_nom=:m_nom, m_prix=:m_prix, m_description=:m_description WHERE m_id = :id";
    $stmt = $this->pdo->prepare($query);
    $result = $stmt->execute([
    'm_nom' => $data['m_nom'],
    'm_prix' => $data['m_prix'],
    'm_description' => $data['m_description'],
    'id' => $id
    ]);

    if ($result && $stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Produit non trouvé'
    ]);
    exit;
    }
    http_response_code(204);
    return $result;
}
return false;
}

public function delete(int $id, string $resourceName, string $isItemOperation, string $httpMethod): bool
{
    if ($resourceName === 'meubles' && $isItemOperation && $httpMethod === 'DELETE') {
        $query = "DELETE FROM meubles WHERE m_id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Produit non trouvé'
            ]);
            exit;
        }
        
        http_response_code(204);
    }
    return true;
}
}