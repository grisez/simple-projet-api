<?php

namespace App\Crud;

use App\Crud\Exception\UnprocessableContentException;
use PDO;

class ElectroniqueCrud
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        // Vérifie que les données obligatoires sont présentes
        $requiredFields = ['e_nom', 'e_prix'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new UnprocessableContentException("$field est obligatoire");
            }
        }

        $query = "INSERT INTO electronique (e_nom, e_prix, e_description) VALUES (:e_nom, :e_prix, :e_description)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'e_nom' => $data['e_nom'],
            'e_prix' => $data['e_prix'],
            'e_description' => $data['e_description'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM electronique");
        $products = $stmt->fetchAll();

        return ($products === false) ? [] : $products;
    }

    public function find(int $id, string $resourceName, string $isItemOperation, string $httpMethod): ?array
    {
        // Vérifie si c'est une opération sur un élément unique de la ressource "electronique" et si la méthode est "GET"
        if ($resourceName === 'electronique' && $isItemOperation && $httpMethod === 'GET') {
            $query = "SELECT * FROM electronique WHERE e_id = :id";
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
        if ($resourceName === 'electronique' && $isItemOperation && $httpMethod === 'PUT') {
            $requiredFields = ['e_nom', 'e_prix'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new UnprocessableContentException("$field est obligatoire");
                }
            }
    
            $query = "UPDATE electronique SET e_nom=:e_nom, e_prix=:e_prix, e_description=:e_description WHERE e_id = :id";
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute([
                'e_nom' => $data['e_nom'],
                'e_prix' => $data['e_prix'],
                'e_description' => $data['e_description'] ?? null,
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
        if ($resourceName === 'electronique' && $isItemOperation && $httpMethod === 'DELETE') {
            $query = "DELETE FROM electronique WHERE e_id = :id";
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
