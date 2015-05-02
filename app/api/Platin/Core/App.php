<?php

namespace Platin\Core;

use Platin\Lib\Request;
use Platin\Lib\Configure;

class App {

  public $Request;
  public $Configure;

  public function __construct($file){
    $this->Request = new Request($file);
    $this->Configure = new Configure();
  }

}

