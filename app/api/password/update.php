<?php

require "utils.php";

header('Content-Type: application/json');

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$result = array("result" => false);
if (updatePassword($data["slug"], toAscii($data["name_edited"]), $data["name_edited"], $data["value_edited"])){
  $result = array("slug" => toAscii($data["name_edited"]), "name" => $data["name_edited"], "value" => $data["value_edited"]);
}

echo json_encode($result, JSON_FORCE_OBJECT);

?>