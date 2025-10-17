<?php

require_once __DIR__ . '/../core/Database.php';

class UsersModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    private function doGen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function getAllUsers()
    {
        try {
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

    public function getDetailProfileUser($masterUserID)
    {
        try {
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

    public function getDetailDocumentUser($masterUserID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM masterusers WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam(":masterUserID", $masterUserID);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("SELECT * FROM users_documents WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam("masterUserID", $masterUserID);
            $stmt->execute();
            $document = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("SELECT link FROM users_miscs WHERE usersDocumentsID = :usersDocumentsID AND deletedDate IS NULL");
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

    public function addUser($fullname, $email, $password, $phoneNumber, $address, $birthPlace, $birthDate, $assignmentPlace, $createdBy)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $createdDate = new DateTime();

            $masterUserID = $this->doGen_uuid();
            $stmt = $this->conn->prepare("
                INSERT INTO `masterusers`
                (`masterUserID`, `fullname`, `email`, `phoneNumber`, `address`, `birthPlace`, `birthDate`, `assignmentPlace`, `createdBy`, `createdDate`)
                VALUES (:masterUserID, :fullname, :email, :phoneNumber, :address, :birthPlace, :birthDate, :assignmentPlace, :createdBy, :createdDate)
            ");
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':birthPlace', $birthPlace);
            $stmt->bindParam(':birthDate', $birthDate);
            $stmt->bindParam(':assignmentPlace', $assignmentPlace);
            $stmt->bindParam(':createdBy', $createdBy);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->execute();

            $authUsersID = $this->doGen_uuid();
            $stmt = $this->conn->prepare("
                INSERT INTO `auth_users`
                (`authUsersID`, `masterUserID`, `password`, `createdBy`, `createdDate`)
                VALUES (:authUsersID, :masterUserID, :password, :createdBy, :createdDate)
            ");
            $stmt->bindParam(':authUsersID', $authUsersID);
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':createdBy', $createdBy);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->execute();

            $usersDocumentsID = $this->doGen_uuid();
            $stmt = $this->conn->prepare("
                INSERT INTO `users_documents`
                (`usersDocumentsID`, `masterUserID`, `createdDate`)
                VALUES (:usersDocumentsID, :masterUserID, :createdDate)
            ");

            $stmt->bindParam(':usersDocumentsID', $usersDocumentsID);
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(":createdBy", $createdBy);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->execute();

            return $this->getDetailDocumentUser($masterUserID);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong" . $e->getMessage(),
                "data" => null,
            ]);
        }
    }
}
