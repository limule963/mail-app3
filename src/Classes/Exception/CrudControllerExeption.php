<?php
    namespace App\Classes\Exception;

use Exception;

    class CrudControllerException extends Exception
    {
        public function __construct($message)
        {
            $this->message = $message;
        }
    }