<?php
require_once "Platin/index.php";
require_once "router.php";

try {
 Platin\Lib\Configure::write("aaa","bbb");
  $App = new Platin\Core\App(__FILE__);
  Platin\Util\Router::execute($App);

} catch (Exception $e) {

  Platin\Util\ExceptionRender::render($App, $e);

}