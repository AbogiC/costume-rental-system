<?php
namespace CostumeRental;

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)"
        );

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bind_param(
            "sssss",
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['role'] ?? 'user',
            $data['phone'] ?? null
        );

        return $stmt->execute();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, phone, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getAllUsers() {
        $query = "SELECT id, name, email, role, phone, created_at FROM users ORDER BY created_at DESC";
        $result = $this->db->query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}