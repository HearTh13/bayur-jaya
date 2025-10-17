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
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
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
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }

    public function getDetailDocumentUser($masterUserID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users_data WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam("masterUserID", $masterUserID);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$dataUser) {
                $stmt = $this->conn->prepare("SELECT link FROM users_documents WHERE usersDataID = :usersDataID AND deletedDate IS NULL");
                $stmt->bindParam(":usersDataID", $dataUser["usersDataID"]);
                $stmt->execute();
                $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $dataUser["documents"] = $documents;
            }

            return $data;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }

    public function addUser($fullname, $phoneNumber, $address, $birthPlaceDate, $assignmentPlace, $createdBy)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $createdDate = (new DateTime())->format(format: 'Y-m-d H:i:s');

            $masterUserID = $this->doGen_uuid();
            $stmt = $this->conn->prepare("
                INSERT INTO `masterusers`
                (`masterUserID`, `fullname`, `phoneNumber`, `address`, role, `birthPlaceDate`, `assignmentPlace`, `createdBy`, `createdDate`)
                VALUES (:masterUserID, :fullname, :phoneNumber, :address, 'user', :birthPlaceDate, :assignmentPlace, :createdBy, :createdDate)
            ");
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':birthPlace', $birthPlaceDate);
            $stmt->bindParam(':assignmentPlace', $assignmentPlace);
            $stmt->bindParam(':createdBy', $createdBy);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->execute();

            $usersDataID = $this->doGen_uuid();
            $stmt = $this->conn->prepare("
                INSERT INTO `users_data`
                (`usersDataID`, `masterUserID`, `createdBy`, `createdDate`)
                VALUES (:usersDataID, :masterUserID, :createdBy, :createdDate)
            ");
            $stmt->bindParam(":usersDataID", $usersDataID);
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(':createdBy', $createdBy);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->execute();

            return $this->getDetailProfileUser($masterUserID);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }

    public function addDocumentByMasterUserID($masterUserID, $modifiedBy, $documents, $name, $departureDate, $place, $batch, $loadAmount, $driverName, $vehicleNo, $description)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $modifiedDate = (new DateTime())->format('Y-m-d H:i:s');

            $stmt = $this->conn->prepare("
                UPDATE `users_data`
                SET 
                    `name` = :name,
                    `departureDate` = :departureDate,
                    `place` = :place,
                    `batch` = :batch,
                    `loadAmount` = :loadAmount,
                    `driverName` = :driverName,
                    `vehicleNo` = :vehicleNo,
                    `description` = :description,
                    `modifiedBy` = :modifiedBy,
                    `modifiedDate` = :modifiedDate
                WHERE `masterUserID` = :masterUserID
            ");
            $stmt->bindParam(':masterUserID', $masterUserID);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':departureDate', $departureDate);
            $stmt->bindParam(':place', $place);
            $stmt->bindParam(':batch', $batch);
            $stmt->bindParam(':loadAmount', $loadAmount);
            $stmt->bindParam(':driverName', $driverName);
            $stmt->bindParam(':vehicleNo', $vehicleNo);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':createdBy', $modifiedBy);
            $stmt->bindParam(':createdDate', $modifiedDate);

            $stmt->execute();

            $stmt = $this->conn->prepare("SELECT * FROM users_data WHERE masterUserID = :masterUserID AND deletedDate IS NULL");
            $stmt->bindParam("masterUserID", $masterUserID);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($documents as $document) {
                $usersDocumentsID = $this->doGen_uuid();
                $stmt = $this->conn->prepare("INSERT INTO users_documents (usersDocumentsID, usersDataID, link, createdBy, createdDate) VALUES (:usersDocumentsID, :usersDataID, :link, :createdBy, :createdDate)");
                $stmt->bindParam(":usersDocumentsID", $usersDocumentsID);
                $stmt->bindParam(":usersDataID", $data["usersDataID"]);
                $stmt->bindParam(":link", $document);
                $stmt->bindParam(":createdBy", $modifiedBy);
                $stmt->bindParam(":createdDate", $modifiedDate);
                $stmt->execute();
            }

            return $this->getDetailDocumentUser($masterUserID);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "something is wrong " . $e->getMessage(),
                "data" => null,
            ]);
            exit;
        }
    }
}
