<?php

require "utils.php";

header('Content-Type: application/json');

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

if (empty($data) || !is_array($data) || !isset($data['username']) || !isset($data['password']) || empty($data['username']) || empty($data['password'])) {
  echo json_encode(array("result" => false, "message" => "Geçersiz kullanıcı adı ve(ya) şifre"), JSON_FORCE_OBJECT);
  exit;
}

$result = false;
$message = "Yanlış kullanıcı adı ve(ya) şifre";
if (loginUser($data["username"], $data["password"])) {
  createSession($data["username"]);
  $result = true;
  $message = "Giriş başarılı";
}

echo json_encode(array('result' => $result, 'message' => $message), JSON_FORCE_OBJECT);

?>