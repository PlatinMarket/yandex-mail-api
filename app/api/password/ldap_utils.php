<?php
require "ldap.php";

checkConfig();

$command = basename($_SERVER["SCRIPT_FILENAME"], '.php');
$publicCommands = array('login', 'logout');

if (!checkSession() && !in_array($command, $publicCommands)) {
  throw new HttpException("Not Authorized", 401);
} else {
  $_SESSION["time"] = time();
}

function checkConfig($file = "config.php"){
  if (!file_exists($file)) {return false;}
  if (defined("LDAP_URILDAP_URI")) {return true;}
  require $file;
  if (defined("LDAP_URILDAP_URI")) {return true;}
  return $false;
}

function connect() {
  if (!checkConfig()) {
    throwError(new Exception("Config file missing!"));
    return false;
  }
  try {
    $ldap = new ldap\LDAP(LDAP_URILDAP_URI, LDAP_BASE_DN, LDAP_SEARCH_DN, LDAP_BASE_DOMAIN);
    return $ldap;
  } catch (Exception $err) {
    throwError($err);
    return false;
  }
}

function loginUser($username, $password) {
  if ($ldap = connect()) {
    try {
      if (!$ldap->authenticate($username, $password)) return false;
      $users = $ldap->get_users();
      return in_array($username, $users);
    } catch (Exception $e) {
      throwError($e);
      return false;
    }
  }
  return false;
}

function checkSession(){
  return isset($_SESSION["user_name"]) && !is_null($_SESSION["user_name"]);
}

function createSession($user_name) {
  $_SESSION["user_name"] = $user_name;
  $_SESSION["time"] = time();
  return true;
}
