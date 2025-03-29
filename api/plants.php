<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$db = getDBConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
            if (isset($_GET['search'])) {
                $search = '%' . $_GET['search'] . '%';
                $stmt = $db->prepare("
                    SELECT id, name, scientific_name, image_path, is_featured 
                    FROM plants 
                    WHERE name LIKE ? OR scientific_name LIKE ?
                    ORDER BY name ASC
                ");
                $stmt->execute([$search, $search]);
                $plants = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'plants' => $plants
                ]);
            }
            else if (isset($_GET['id'])) {
            // Get single plant with categories
            $stmt = $db->prepare("
                SELECT p.*, 
                       GROUP_CONCAT(pc.name) as categories
                FROM plants p
                LEFT JOIN plant_category_map pcm ON p.id = pcm.plant_id
                LEFT JOIN plant_categories pc ON pcm.category_id = pc.id
                WHERE p.id = ?
                GROUP BY p.id
            ");
            $stmt->execute([$_GET['id']]);
            $plant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($plant) {
                // Convert categories string to array
                $plant['categories'] = $plant['categories'] ? 
                    explode(',', $plant['categories']) : [];
                
                echo json_encode([
                    'success' => true,
                    'plant' => $plant
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Plant not found'
                ]);
            }
        } else {
            // Get all plants (basic info only)
            $featured = isset($_GET['featured']) ? 1 : null;
            
            $query = "SELECT id, name, scientific_name, image_path, is_featured FROM plants";
            if ($featured !== null) {
                $query .= " WHERE is_featured = 1";
            }
            $query .= " ORDER BY name ASC";
            
            $stmt = $db->query($query);
            $plants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'plants' => $plants
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
}