<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Token, Authorization');
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}
