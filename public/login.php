<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
    http_response_code(400);
    echo json_encode([
        "message" => "Bad request",
        "data" => null
    ]);
}

$controller = new AuthController();
$controller->login($data["email"], $data["password"]);
