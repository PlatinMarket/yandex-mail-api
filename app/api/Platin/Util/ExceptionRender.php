<?php

namespace Platin\Util;

use Platin\Exception\HttpException;

class ExceptionRender {

  static function render($App, $exception) {
    $code = 500;
    if ($exception instanceof HttpException) $code = $exception->getCode();

    http_response_code($code);
    header("Content-Type: text/plain");
    echo $exception->getMessage();
  }

}