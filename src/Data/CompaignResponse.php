<?php

namespace App\Data;


    class CompaignResponse
    {
        public array $stat;

        public function setStat(EmailResponse $em)
        {
            $this->stat[] = [
                'email' => $em->leadEmail,
                'status'=> $em->message,
                'sender'=>$em->sender,
                'step'=>$em->stepStatus,
                'throw Message' =>$em->throwMessage
            ];

        }
    }