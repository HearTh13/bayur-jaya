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

    public function getDetailProfileUser($masterUserID){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM masterusers WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam(":masterUserID", $masterUserID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong" . $e->getMessage(),
                "data" => null,
            ]);
        }
    }

    public function getDetailDocumentUser($masterUserID){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM masterusers WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam(":masterUserID", $masterUserID);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("SELECT * FROM users_documents WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam("masterUserID", $masterUserID);
            $stmt->execute();
            $document = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("SELECT link FROM users_miscs WHERE usersDocumentsID = :usersDocumentsID");
            $stmt->bindParam(":usersDocumentsID", $document["usersDocumentsID"]);
            $stmt->execute();
            $misc = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = [
                "masterUserID" => $user["masterUserID"],
                "fullname" => $user["fullname"],
                "departureDate" => $document["departureDate"],
                "place" => $document["place"],
                "miscs" => $misc,
                "batch" => $document["batch"],
                "loadAmount" => $document["loadAmount"],
                "driverName" => $document["driverName"],
                "vehicleNo" => $document["vehicleNo"],
                "description" => $document["description"]
            ];
            return $data;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong" . $e->getMessage(),
                "data" => null,
            ]);
        }
    }
}