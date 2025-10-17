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
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }

    public function getPasswordByMasterUserID($masterUserID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT password FROM auth_users WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam(":masterUserID", $masterUserID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)["password"];
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }

    public function addToken($accessToken, $masterUserID){
        try{
            $stmt = $this->conn->prepare("UPDATE auth_users SET `accessToken` = :accessToken WHERE masterUserID = :masterUserID");
            $stmt->bindParam(":accessToken", $accessToken);
            $stmt->bindParam(":masterUserID", $masterUserID);
            $stmt->execute();
        }catch(PDOException $e){
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }
}
