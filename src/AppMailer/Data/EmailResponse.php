<?php
    
namespace App\AppMailer\Data;
    

    class EmailResponse
    {
        
        public bool $succes;
        public string $message;
        public string $throwMessage;
        public string $sender;
        public int $code;
        public string $leadEmail;
        public string $stepStatus;

        public function __construct($succes,$message,$sender,$leadEmail,$stepStatus= '',int $code = 1,$throwMessage ='')
        {
            $this->succes = $succes;
            $this->message =$message;
            $this->throwMessage =$throwMessage;
            $this->sender = $sender;
            $this->stepStatus = $stepStatus;
            $this->code = $code;
            $this->leadEmail = $leadEmail;
        }
    }