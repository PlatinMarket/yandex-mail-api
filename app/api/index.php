<?php
require_once "Vendor/autoload.php";
require_once "Platin/index.php";
require_once "router.php";
require_once "config.php";

try {

  $App = new Platin\Core\App(__FILE__);
  Platin\Util\Router::execute($App);

} catch (Exception $e) {

  Platin\Util\ExceptionRender::render($App, $e);

}