<?php

namespace Platin\Db;

class MysqlDatabase {

  protected static $_connections = array();

  public static function register($name = "default", $config = array()) {
    if (self::isRegistered($name)) throw new Platin\Exception\MysqlException("Connection '" . $name . "' already exists in pool");
    self::$_connections[$name] = new \Simplon\Mysql\Mysql($config);
  }

  public static function isRegistered($name = "default") {
    return isset(self::$_connections[$name]);
  }

  public static function isConnected($name = "default") {
    if (!isset(self::$_connections[$name])) return false;
    return true;
  }



}