<?php

namespace App\Data;


    class CompaignResponse
    {
        public array $stat;

        public function setStat(?EmailResponse $em,$compaignState)
        {
            if($em != null)
            {
                $this->stat[] = $em;
                $this->stat['compaignStatus'] = $compaignState;
                // $this->stat[] = [
                //     'email' => $em->leadEmail,
                //     'status'=> $em->message,
                //     'sender'=>$em->sender,
                //     'step'=>$em->stepStatus,
                //     'throw Message' =>$em->throwMessage,
                //     'compaignStatus'=>$compaignState
                // ];

            }
            else 
            $this->stat['compaignStatus'] = $compaignState;

        }
    }