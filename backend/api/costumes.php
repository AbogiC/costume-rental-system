<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Costume.php';

$database = new Database();
$db = $database->getConnection();
$costumeModel = new \CostumeRental\Costume($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $costume = $costumeModel->getCostumeById($_GET['id']);
            if ($costume) {
                echo json_encode($costume);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Costume not found."]);
            }
        } else {
            $costumes = $costumeModel->getAllCostumes();
            echo json_encode($costumes);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($costumeModel->createCostume($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Costume created successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create costume."]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $_GET['id'] ?? null;
        
        if ($id && $costumeModel->updateCostume($id, $data)) {
            echo json_encode(["message" => "Costume updated successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update costume."]);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        
        if ($id && $costumeModel->deleteCostume($id)) {
            echo json_encode(["message" => "Costume deleted successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete costume."]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}

$database->close();
?>