<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';
include __DIR__ . '/../header.php';

$data = json_decode(file_get_contents("php://input"), true);

$requiredFields = [
    "fullname",
    "email",
    "phoneNumber",
    "address",
    "birthPlaceDate",
    "tempatPenugasan",
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode([
            "message" => "Bad request",
            "data" => $field,
        ]);
        exit;
    }
}

$controller = new UsersController();
$controller->addUser(
    $data["fullname"],
    $data["email"],
    $data["phoneNumber"],
    $data["address"],
    $data["birthPlaceDate"],
    $data["tempatPenugasan"]
);
