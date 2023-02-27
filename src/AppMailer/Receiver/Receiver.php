<?php

namespace App\AppMailer\Receiver;
use PhpImap\Mailbox;
use SecIT\ImapBundle\Service\Imap;


    class Receiver
    {

        public function __construct()
        {
            
        }

        public function test()
        {

            $connexions = [
                'clemaos'=>[
                    
                    'mailbox'=>"{mail51.lwspanel.com:993/imap/ssl}INBOX",
                    'username'=>'contact@clemaos.com',
                    'password'=>'jC1*rAJ8GGph9@u'
                ],

                'aykode'=>[
                    'mailbox'=>"{mail56.lwspanel.com:993/imap/ssl}INBOX",
                    'username'=>'contact@aykode.com',
                    'password'=>'dF4_pxe9t!5q_wa'

                ],
                
                'crubeo'=>[
                    'mailbox'=>"{mail52.lwspanel.com:993/imap/ssl}INBOX",
                    'username'=>'contact@crubeo.fr',
                    'password'=>'rS1*aahSMZhKq9q'
                ]
            ];

            $imap = new Imap($connexions);
            $mailbox = $imap->get('clemaos');
            $isConnectable = $imap->testConnection('clemaos');
            


        }
    }   