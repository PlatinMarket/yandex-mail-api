<?php

namespace Platin\Exception;

use Exception;

class MysqlException {

  private $name = "Mysql";

  public function __construct($message, $code = 0, Exception $previous = null) {
      parent::__construct($message, $code, $previous);
      $this->file = "Mysql";
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