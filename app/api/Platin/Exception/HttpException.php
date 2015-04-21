<?php

namespace Platin\Exception;

use Exception;

class HttpException extends Exception {

    private $name = "Http";

    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function out($type = 'array') {
      if ($type == "array") {
        return array(
          "code" => $this->code,
          "message" => $this->message,
          "uri" => Platin\Lib\Configure::read("path")
        );
      }
    }
}