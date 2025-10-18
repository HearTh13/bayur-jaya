<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';
include __DIR__ . '/../header.php';

$controller = new UsersController();
$controller->getAllUsers();