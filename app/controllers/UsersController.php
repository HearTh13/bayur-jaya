<?php

require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../models/UsersModel.php';

class UsersController
{
    public function getAllUsers()
    {
        $user = AuthMiddleware::authenticate();
        $usersModel = new UsersModel();
        $data = $usersModel->getAllUsers();
        http_response_code(200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }

    public function getUserDetail()
    {
        $user = AuthMiddleware::authenticate();
        $usersModel = new UsersModel();
        $data = $usersModel->getDetailProfileUser($user["masterUserID"]);
        http_response_code(200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }

    public function getDocument(){
        $user = AuthMiddleware::authenticate();
        $userModel = new UsersModel();
        $data = $userModel->getDetailDocumentUser($user["masterUserID"]);
        http_response_code(200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }

    public function addUser($fullname, $email, $password, $phoneNumber, $address, $birthPlace, $birthDate, $assignmentPlace, $createdBy){
        $user = AuthMiddleware::authenticate();
        $userModel = new UsersModel();
        $data = $userModel->addUser($fullname, $email, $password, $phoneNumber, $address, $birthPlace, $birthDate, $assignmentPlace, $createdBy);
        http_response_code(200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }
}
