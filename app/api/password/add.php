<?php

require "utils.php";

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

header('Content-Type: application/json');

$result = array("result" => false);
if (addPassword(toAscii($data["name"]), $data["name"], $data["value"])){
  $result = array("slug" => toAscii($data["name"]), "name" => $data["name"], "value" => $data["value"]);
}

echo json_encode($result, JSON_FORCE_OBJECT);

?>