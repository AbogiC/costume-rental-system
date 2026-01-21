<?php
namespace CostumeRental;

class Costume {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllCostumes() {
        $query = "SELECT * FROM costumes ORDER BY created_at DESC";
        $result = $this->db->query($query);
        
        $costumes = [];
        while ($row = $result->fetch_assoc()) {
            $costumes[] = $row;
        }
        
        return $costumes;
    }

    public function getCostumeById($id) {
        $stmt = $this->db->prepare("SELECT * FROM costumes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function createCostume($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO costumes (name, description, category, size, price_per_day, quantity_available, image_url) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "ssssdis",
            $data['name'],
            $data['description'],
            $data['category'],
            $data['size'],
            $data['price_per_day'],
            $data['quantity_available'],
            $data['image_url']
        );
        
        return $stmt->execute();
    }

    public function updateCostume($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE costumes SET 
                name = ?,
                description = ?,
                category = ?,
                size = ?,
                price_per_day = ?,
                quantity_available = ?,
                image_url = ?
             WHERE id = ?"
        );
        
        $stmt->bind_param(
            "ssssdisi",
            $data['name'],
            $data['description'],
            $data['category'],
            $data['size'],
            $data['price_per_day'],
            $data['quantity_available'],
            $data['image_url'],
            $id
        );
        
        return $stmt->execute();
    }

    public function deleteCostume($id) {
        $stmt = $this->db->prepare("DELETE FROM costumes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>