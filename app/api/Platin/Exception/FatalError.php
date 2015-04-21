<?php


namespace Platin\Exception;
require 'HttpException.php';

class FatalErrorException extends HttpException {
    
    private $name = "FatalError";
}