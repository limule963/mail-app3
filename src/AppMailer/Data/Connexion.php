<?php

namespace App\AppMailer\Data;
use App\Entity\Dsn;

    class Connexion
    {
        public function __construct(
            public Dsn $dsn,
            public $folder
        )
        {
            
        }
    }