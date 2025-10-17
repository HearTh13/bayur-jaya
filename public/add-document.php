<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';

$controller = new UsersController();

$requiredFields = [
    "documents"
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode([
            "message" => "Bad Request",
            "data" => $field,
        ]);
        exit;
    }
}

$controller->addUserDocument($data["documents"]);
