<?php
set_exception_handler('exception_handler');

session_start();
$_SESSION["last_exception"] = null;

require "ldap_utils.php";

if (!defined('FILE')) define('FILE', "password_file.dat");

if (!file_exists(FILE)) { file_put_contents(FILE, serialize(array())); }

function getPasswords(){
  return unserialize(file_get_contents(FILE));
}

function addPassword($slug, $name, $value){
  $passwords = getPasswords();
  if (isset($passwords[$slug])) { return false; }
  $passwords[$slug] = array('name' => $name, 'value' => $value, 'slug' => $slug);
  if (commitChanges($passwords)) { return true; }
  return false;
}

function updatePassword($slugOld, $slugNew, $name, $value){
  $passwords = getPasswords();
  if (!isset($passwords[$slugOld])) { return false; }
  unset($passwords[$slugOld]);
  $passwords[$slugNew] = array('name' => $name, 'value' => $value, 'slug' => $slugNew);
  if (commitChanges($passwords)) { return true; }
  return false;
}

function deletePassword($slug){
  $passwords = getPasswords();
  if (!isset($passwords[$slug])) { return false; }
  unset($passwords[$slug]);
  if (commitChanges($passwords)) { return true; }
  return false;
}

function throwError($err){
  if (!isset($_SESSION["last_exception"])) $_SESSION["last_exception"] = array();
  $_SESSION["last_exception"][] = $err;
}

function readError(){
  return $_SESSION["last_exception"];
}

function commitChanges($passwords){
  if (!file_exists(FILE)) { file_put_contents(FILE, serialize(array())); }
  try {
    file_put_contents(FILE, serialize($passwords));
    return true;
  } catch (Exception $err) {
    return false;
  }
}

function isAjax(){
  return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function exception_handler($exception) {
  $code = 500;
  $header = "Fatal Error";

  if (get_class($exception) == "HttpException") {
    $code = $exception->getCode();
    $header = get_class($exception);
  }

  http_response_code($code);

  if (isAjax()){
    header('Content-Type: application/json');
    echo json_encode(
      array(
        "code" => $exception->getCode(),
        "message" => $exception->getMessage()
      )
    );
    return;
  }
  header('Content-Type: text/plain');
  echo $header . ": " . $exception->getMessage();
}

setlocale(LC_ALL, 'en_US.UTF8');
function toAscii($str, $replace=array(), $delimiter='-') {
  if( !empty($replace) ) {
    $str = str_replace((array)$replace, ' ', $str);
  }

  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower(trim($clean, '-'));
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

  return $clean;
}

class HttpException extends Exception {
  
  public function __construct($message, $code = 0, Exception $previous = null) {
    // some code

    // make sure everything is assigned properly
    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

}

?>
