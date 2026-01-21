<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

switch ($method) {
    case 'GET':
        $query = "SELECT r.*, c.name as costume_name, u.name as user_name, u.email as user_email
                  FROM rentals r
                  JOIN costumes c ON r.costume_id = c.id
                  JOIN users u ON r.user_id = u.id
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

        // Check if this is a status update
        if (isset($data['action']) && $data['action'] === 'update_status') {
            $required = ['id', 'status'];
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
                // Get current rental and costume info
                $stmt = $db->prepare(
                    "SELECT r.status, r.costume_id, c.quantity_available
                     FROM rentals r
                     JOIN costumes c ON r.costume_id = c.id
                     WHERE r.id = ? FOR UPDATE"
                );
                $stmt->bind_param("i", $data['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $rental = $result->fetch_assoc();

                if (!$rental) {
                    throw new Exception("Rental not found");
                }

                // Update rental status
                $stmt = $db->prepare("UPDATE rentals SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $data['status'], $data['id']);

                if (!$stmt->execute()) {
                    throw new Exception("Failed to update rental status");
                }

                // Update costume quantity based on status change
                if ($rental['status'] === 'pending' && $data['status'] === 'active') {
                    // Approve: decrement quantity
                    if ($rental['quantity_available'] <= 0) {
                        throw new Exception("Costume not available");
                    }
                    $stmt = $db->prepare("UPDATE costumes SET quantity_available = quantity_available - 1 WHERE id = ?");
                    $stmt->bind_param("i", $rental['costume_id']);
                } elseif ($rental['status'] === 'active' && $data['status'] === 'cancelled') {
                    // Cancel active rental: increment quantity
                    $stmt = $db->prepare("UPDATE costumes SET quantity_available = quantity_available + 1 WHERE id = ?");
                    $stmt->bind_param("i", $rental['costume_id']);
                }

                if (isset($stmt) && !$stmt->execute()) {
                    throw new Exception("Failed to update costume quantity");
                }

                $db->commit();
                echo json_encode(["message" => "Rental status updated successfully"]);

            } catch (Exception $e) {
                $db->rollback();
                http_response_code(500);
                echo json_encode(["message" => "Failed to update rental status: " . $e->getMessage()]);
            }
            break;
        }

        // Validate required fields for new rental
        $required = ['user_id', 'costume_id', 'rental_date', 'return_date'];
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
                "INSERT INTO rentals (user_id, costume_id, rental_date, return_date, total_price, status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            );

            $stmt->bind_param(
                "iissd",
                $data['user_id'],
                $data['costume_id'],
                $data['rental_date'],
                $data['return_date'],
                $total_price
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to create rental");
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