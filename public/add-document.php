<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';

$controller = new UsersController();

$requiredFields = [
    "documents"
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(500);
        echo json_encode([
            "message" => "something is wrong " . $e->getMessage(),
            "data" => null,
        ]);
        exit;
    }
}

$controller->addUserDocument($data["documents"]);
