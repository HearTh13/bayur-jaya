<?php

require_once __DIR__ . '/../models/AuthModel.php';

class AuthController
{
    public function login($email, $password)
    {
        $authModel = new AuthModel();
        $data = $authModel->findUserByEmail($email);
        if ($data) {
            $dataPass = $authModel->getPasswordByMasterUserID($data["masterUserID"]);
            if (password_verify($password, $dataPass)) {
                http_response_code(200);
                echo json_encode([
                    "message" => "Autentikasi berhasil",
                    "data" => $password,
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    "message" => "User Authentication Failed: Wrong User or Password",
                    "data" => null,
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "User Authentication Failed: Wrong User or Password",
                "data" => null,
            ]);
        }
    }
}
