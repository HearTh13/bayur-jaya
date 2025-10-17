<?php

require_once __DIR__ . '/../core/Database.php';

class UsersModel{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function getAllUsers(){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM masterusers WHERE deletedDate IS NULL");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong" . $e->getMessage(),
                "data" => null,
            ]);
        }
    }
}