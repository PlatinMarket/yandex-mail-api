<?php

namespace Platin\Util;

use Platin\Lib\Configure;

class Router {
  
  private static $routes = array();
  private static $App;
  
  private function __construct() {}
  private function __clone() {}
  
  public static function route($pattern, $callback) {
    $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
    self::$routes[$pattern] = $callback;
  }
  
  public static function execute($App) {
    self::$App = $App;
    foreach (self::$routes as $pattern => $callback) {
      if (preg_match($pattern, self::$App->Request->get("path"), $params)) {
        array_shift($params);
        return call_user_func_array($callback, array_merge(array(self::$App), array_values($params)));
      }
    }
  }

}