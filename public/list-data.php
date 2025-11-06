<?php

require_once __DIR__ . '/../app/controllers/UsersController.php';
include __DIR__ . '/../header.php';

$controller = new UsersController();
if (!isset($_GET["masterUserID"]) || $_GET["masterUserID"] === null){
    $masterUserID = null;
} else {
    $masterUserID = $_GET["masterUserID"];
}
if (!isset($_GET["startDate"]) || $_GET["startDate"] === null){
    $date = null;
} else {
    $date = $_GET["startDate"];
}
$controller->getDocument($masterUserID, $date);