<?php

namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use PhpImap\Mailbox;
use App\AppMailer\Data\Connexion;
use SecIT\ImapBundle\Service\Imap;


    class Receiver
    {
        public const INBOX = 'INBOX';
        public const SENT = 'Sent';
        public const DRAFTS = 'Drafts';
        public const JUNK = 'Junk';

        public function __construct()
        {
            
        }

        public function getMail(Connexion $connexion)
        {

        $dsn = $connexion->dsn;
        $folder = $connexion->folder;

        $imap = new Imap($dsn->getConnexion());
        $con = $imap->get($dsn->getConnexionName());

        $stamp = $dsn->getCreateAt()->getTimestamp();
        $date =getdate($stamp);

        $con->switchMailbox($folder);
        $criteria = 'since '.$date['year'].'-'.$date['mon'].'-'.$date['mday'];
        $mailIds = $con->searchMailbox($criteria);



        }
    }   