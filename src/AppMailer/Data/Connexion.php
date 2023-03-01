<?php

namespace App\AppMailer\Data;
use App\Entity\Dsn;

    class Connexion
    {
        public string $folder;
        public string $criteria;
        public Dsn $dsn;
        public function __construct(
        )
        {
            
        }
        public function set(Dsn $dsn,string $criteria = 'All',string $folder = FOLDER::INBOX):self
        {
            $this->dsn = $dsn;
            $this->folder = $folder;
            $this->criteria = $criteria;
            return $this;
        }
    }