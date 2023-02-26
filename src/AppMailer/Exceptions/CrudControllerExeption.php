<?php
namespace App\AppMailer\Exceptions;
    

use Exception;

    class CrudControllerException extends Exception
    {
        public function __construct($message)
        {
            $this->message = $message;
        }
    }