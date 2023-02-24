<?php
    
    namespace App\Data;

    class EmailResponse
    {
        
        public bool $succes;
        public string $message;
        public string $throwMessage;
        public string $sender;
        public int $code;
        public string $leadEmail;

        public function __construct($succes,$message,$sender,$leadEmail,int $code = 1,$throwMessage ='')
        {
            $this->succes = $succes;
            $this->message =$message;
            $this->throwMessage =$throwMessage;
            $this->sender = $sender;
            $this->code = $code;
            $this->leadEmail = $leadEmail;
        }
    }