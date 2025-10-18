<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
include __DIR__ . '/../header.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
    http_response_code(400);
    echo json_encode([
        "message" => "Bad request",
        "data" => $field
    ]);
}

$controller = new AuthController();
$controller->login($data["email"], $data["password"]);
