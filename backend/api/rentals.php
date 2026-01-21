<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query = "SELECT r.*, c.name as costume_name FROM rentals r 
                  JOIN costumes c ON r.costume_id = c.id 
                  ORDER BY r.created_at DESC";
        $result = $db->query($query);
        
        $rentals = [];
        while ($row = $result->fetch_assoc()) {
            $rentals[] = $row;
        }
        
        echo json_encode($rentals);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        $required = ['costume_id', 'renter_name', 'renter_email', 'rental_date', 'return_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(["message" => "Missing required field: $field"]);
                exit;
            }
        }
        
        // Start transaction
        $db->begin_transaction();
        
        try {
            // Check costume availability
            $stmt = $db->prepare("SELECT quantity_available, price_per_day FROM costumes WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $data['costume_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $costume = $result->fetch_assoc();
            
            if (!$costume || $costume['quantity_available'] < 1) {
                throw new Exception("Costume not available for rent");
            }
            
            // Calculate total price
            $rental_date = new DateTime($data['rental_date']);
            $return_date = new DateTime($data['return_date']);
            $days = $return_date->diff($rental_date)->days + 1;
            $total_price = $days * $costume['price_per_day'];
            
            // Create rental
            $stmt = $db->prepare(
                "INSERT INTO rentals (costume_id, renter_name, renter_email, renter_phone, 
                                      rental_date, return_date, total_price, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
            );
            
            $stmt->bind_param(
                "isssssd",
                $data['costume_id'],
                $data['renter_name'],
                $data['renter_email'],
                $data['renter_phone'] ?? null,
                $data['rental_date'],
                $data['return_date'],
                $total_price
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create rental");
            }
            
            // Update costume quantity
            $stmt = $db->prepare("UPDATE costumes SET quantity_available = quantity_available - 1 WHERE id = ?");
            $stmt->bind_param("i", $data['costume_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update costume quantity");
            }
            
            $db->commit();
            
            http_response_code(201);
            echo json_encode([
                "message" => "Rental created successfully",
                "total_price" => $total_price,
                "rental_id" => $stmt->insert_id
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            http_response_code(500);
            echo json_encode(["message" => "Rental failed: " . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

$database->close();
?>