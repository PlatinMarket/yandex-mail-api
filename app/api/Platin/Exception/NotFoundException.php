<?php

namespace Platin\Exception;


class NotFoundException extends HttpException {
    
    private $name = "NotFound";

    public function __construct($message, $code = 404, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->code = 404;
        $this->file = $message;
        $this->message = "Requested page '/" . $message . "' not found";
    }

}