<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';

$controller = new UsersController();

$data = json_decode(file_get_contents("php://input"), true);

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

// Jalankan fungsi controller
$controller->addUserDocument($data["documents"]);
