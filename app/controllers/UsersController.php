<?php

require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../models/UsersModel.php';

class UsersController
{
    public function getAllUsers()
    {
        $user = AuthMiddleware::authenticate();
        if ($user["role"] === "admin") {
            $usersModel = new UsersModel();
            $data = $usersModel->getAllUsers();
            http_response_code(200);
            echo json_encode([
                "message" => "Data User berhasil diambil",
                "data" => $data,
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Unauthorized",
                "data" => null,
            ]);
        }
    }

    public function getUserDetail($masterUserID)
    {
        $user = AuthMiddleware::authenticate();

        $usersModel = new UsersModel();
        $data = $usersModel->getDetailProfileUser($masterUserID);

        if ($user["role"] === 'admin') {
            http_response_code(200);
            echo json_encode([
                "message" => "Data User berhasil diambil",
                "data" => $data,
            ]);
        } else {
            if ($user["masterUserID"] === $data["masterUserID"]) {
                http_response_code(200);
                echo json_encode([
                    "message" => "Data User berhasil diambil",
                    "data" => $data,
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    "message" => "Unauthorized",
                    "data" => null,
                ]);
            }
        }
    }

    public function getDocument()
    {
        $user = AuthMiddleware::authenticate();
        $userModel = new UsersModel();
        $data = $userModel->getDetailDocumentUser();
        http_response_code(response_code: 200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }

    public function addUser($fullname, $email, $phoneNumber, $address, $birthPlaceDate, $assignmentPlace)
    {
        $user = AuthMiddleware::authenticate();
        if ($user["role"] === "admin") {
            $userModel = new UsersModel();
            $data = $userModel->addUser($fullname, $email, $phoneNumber, $address, $birthPlaceDate, $assignmentPlace, $user["masterUserID"]);
            http_response_code(200);
            echo json_encode([
                "message" => "Data User berhasil ditambahkan",
                "data" => $data,
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Unauthorized",
                "data" => null,
            ]);
        }
    }

    public function addUserDocument($documents, $masterUserID, $departureDate, $place, $batch, $loadAmount, $driverName, $vehicleNo, $description)
    {
        $user = AuthMiddleware::authenticate();
        $userModel = new UsersModel();
        $userData = $userModel->getDetailProfileUser($masterUserID);
        $data = $userModel->addDocumentByMasterUserID($userData["masterUserID"], $user["masterUserID"], $documents, $departureDate, $place, $batch, $loadAmount, $driverName, $vehicleNo, $description);
        http_response_code(200);
        echo json_encode([
            "message" => "Dokumen User berhasil ditambahkan",
            "data" => $data,
        ]);
    }
}
