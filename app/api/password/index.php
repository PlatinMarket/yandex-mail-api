<?php

require "utils.php";

header('Content-Type: application/json');

$passwords = array();
foreach (getPasswords() as $key => $value) {
  $passwords[] = array('name' => $value['name'], 'value' => $value['value'], 'slug' => $key);
}

echo json_encode($passwords, false);


?>