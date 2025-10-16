<?php

require_once __DIR__ . '/../core/Database.php';

class AuthModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function findUserByEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM masterusers WHERE email = :email AND deletedDate IS NULL");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode([
                "message" => "something is wrong" . $e->getMessage(),
                "data" => null,
            ]);
        }
    }
}
