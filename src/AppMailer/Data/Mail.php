<?php
    
    namespace App\AppMailer\Data;

    class Mail
    {
        
        public int $mid;
        public string $subject;
        public string $from;
        public string $to;
        public string $date;
        public bool $isRecent;
        public bool $isFlagged;
        public bool $isAnswered;
        public bool $isDeleted;
        public bool $isSeen;
        public bool $isDraft;
        public string $textHtml;
        public string $textPlain;
        public string $udate;
      

        public function __construct(

            
        )
        {
            
        }
    }