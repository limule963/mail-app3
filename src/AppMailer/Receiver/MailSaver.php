<?php
    
    namespace App\AppMailer\Receiver;

use App\AppMailer\Data\FOLDER;
use App\Entity\Dsn;
use App\Controller\CrudControllerHelpers;

    class MailSaver
    {
        public function __construct(private AllDsnReceiver $allDsnRec,private CrudControllerHelpers $crud,private AllFolderReceiver $allrec)
        {
            
        }

        public function saveMails($mails)
        {

            foreach($mails as $key => $mail)
            {

                if(empty($mail)) continue;
                else
                {

                }
                // if($key == FOLDER::JUNK && $mails[FOLDER::JUNK] != null)
                // {
                //     foreach($mails[FOLDER::JUNK])
                //     {

                //     }
                //     }
                // }
                // else if($key == FOLDER::INBOX && $mails[FOLDER::INBOX] != null)
                // {
                //     foreach($mails[FOLDER::JUNK])
                //     {

                //     }
                // }

            }
            
        }
    }