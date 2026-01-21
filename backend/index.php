<?php
// Simple API Router for Costume Rental System

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request URI and remove query string
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base path if needed (adjust based on your setup)
// $basePath = '/backend';
// if (strpos($requestUri, $basePath) === 0) {
//     $requestUri = substr($requestUri, strlen($basePath));
// }

// Route the request
switch ($requestUri) {
    case '/auth.php':
    case '/api/auth.php':
        require_once 'api/auth.php';
        break;

    case '/costumes.php':
    case '/api/costumes.php':
        require_once 'api/costumes.php';
        break;

    case '/rentals.php':
    case '/api/rentals.php':
        require_once 'api/rentals.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "API endpoint not found"]);
        break;
}
?>