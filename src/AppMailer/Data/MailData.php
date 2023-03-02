<?php

namespace App\AppMailer\Data;
use App\Entity\Dsn;

    class MailData
    {
        public Dsn $dsn;
        public int $compaignId;
        public mixed $criteria;
        public function __construct(

        )
        {
            
        }

        public function set(Dsn $dsn,int $compaignId,mixed $criteria):self
        {
            $this->dsn = $dsn;
            $this->compaignId =$compaignId;
            $this->criteria= $criteria;

            return $this;
        }
    }