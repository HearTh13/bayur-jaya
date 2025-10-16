<?php

require_once __DIR__ . '/../models/AuthModel.php';

class AuthController
{
    public function login($email, $password)
    {
        $authModel = new AuthModel();
        $data = $authModel->findUserByEmail($email);
        echo json_encode([
            "message" => "autentikasi berhasil",
            "data" => $data,
        ]);
    }
}
