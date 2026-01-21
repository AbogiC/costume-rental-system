<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new \CostumeRental\User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $action = $data['action'] ?? '';

        if ($action === 'register') {
            // Register
            $required = ['name', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Missing required field: $field"]);
                    exit;
                }
            }

            // Check if email exists
            if ($userModel->getUserByEmail($data['email'])) {
                http_response_code(409);
                echo json_encode(["message" => "Email already exists"]);
                exit;
            }

            if ($userModel->createUser($data)) {
                http_response_code(201);
                echo json_encode(["message" => "User registered successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to register user"]);
            }
        } elseif ($action === 'login') {
            // Login
            $required = ['email', 'password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(["message" => "Missing required field: $field"]);
                    exit;
                }
            }

            $user = $userModel->getUserByEmail($data['email']);
            if ($user && $userModel->verifyPassword($data['password'], $user['password'])) {
                unset($user['password']); // Remove password from response
                echo json_encode([
                    "message" => "Login successful",
                    "user" => $user
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid credentials"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid action"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

$database->close();
?>