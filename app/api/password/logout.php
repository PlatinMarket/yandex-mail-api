<?php

require "utils.php";

header('Content-Type: application/json');

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$result = true;
session_destroy();

echo json_encode(array('result' => $result), JSON_FORCE_OBJECT);

?>