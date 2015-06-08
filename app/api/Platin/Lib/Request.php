<?php

namespace Platin\Lib;

use Platin\Lib;

class Request {

  protected $_webroot;
  protected $_path;
  protected $_query_string;
  protected $_query = array();
  protected $_body = array();

  public function __construct($file) {
    $this->_webroot = str_replace("/" . basename($file), "", Hash::get($_SERVER, "SCRIPT_NAME"));
    $this->_query_string = Hash::get($_SERVER, "QUERY_STRING");
    $this->_path = str_replace($this->_webroot, "", Hash::get($_SERVER, "REQUEST_URI"));
    $this->_path = str_replace("?" . $this->_query_string, "", $this->_path);
    $this->_query = $this->_parseQuery();
    $this->_body = $this->_parseBody();
  }

  public function get($key) {
    $key = "_" . $key;
    if (property_exists($this, $key)) return $this->{$key};
    return null;
  }

  public function fromLocal(){
    if (!isset($_SERVER['LOCAL_ADDR'])) return $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'];
    if (!isset($_SERVER['SERVER_ADDR'])) return $_SERVER['LOCAL_ADDR'] == $_SERVER['REMOTE_ADDR'];
    return ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'] || $_SERVER['LOCAL_ADDR'] == $_SERVER['REMOTE_ADDR']);
  }

  private function _parseQuery(){
    return $_GET; //must more secure
  }

  private function _parseBody(){
    return $_POST; // must more secure
  }

  public function body($key = "") {
    return Hash::get($this->_body, $key);
  }

  public function query($key = "") {
    return Hash::get($this->_query, $key);
  }

  public function isAjax(){
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
  }

  public function clientIp(){
    return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  }

}
