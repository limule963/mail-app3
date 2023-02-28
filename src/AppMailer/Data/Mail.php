<?php
    
    namespace App\AppMailer\Data;

    class Mail
    {
      
      

        public function __construct(

            
            public string $from,
            public string $to,
            public string $subject,
            public \DateTimeImmutable $date,
            public string $textHtml,
            public string $textPlain,
            public bool $isAnswered
        )
        {
            
        }
    }