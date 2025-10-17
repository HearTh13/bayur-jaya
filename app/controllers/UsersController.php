<?php

require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../models/UsersModel.php';

class UsersController
{
    public function getAllUsers()
    {
        AuthMiddleware::authenticate();
        $usersModel = new UsersModel();
        $data = $usersModel->getAllUsers();
        http_response_code(200);
        echo json_encode([
            "message" => "Data User berhasil diambil",
            "data" => $data,
        ]);
    }
}
