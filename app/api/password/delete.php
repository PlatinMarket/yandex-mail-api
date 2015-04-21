<?php

require "utils.php";

header('Content-Type: application/json');

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$result = array("result" => false);
if (deletePassword($data["slug"])) {
  $result = array("result" => true);
}

echo json_encode($result, JSON_FORCE_OBJECT);
?>