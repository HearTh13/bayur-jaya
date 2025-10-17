<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';

if (!isset($_GET["masterUserID"])) {
    http_response_code(400);
    echo json_encode([
        "message" => "Bad request",
        "data" => $field,
    ]);
    exit;
}

$controller = new UsersController();
$controller->getUserDetail($_GET["masterUserID"]);
