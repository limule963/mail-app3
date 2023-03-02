<?php
    
    namespace App\AppMailer\Receiver;

    class ImapCurl
    {   public $host = 'mail51.lwspanel.com';
        public $user = 'contact@clemaos.com';
        public $pass = 'jC1*rAJ8GGph9@u';
        public $folder = 'INBOX';


        public function __construct()
        {
            
        }
        public function setParam($host,$user,$pass,$folder):self
        {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;
            $this->folder = $folder;

            return $this;
        }




        public function send_imap_command($server, $user, $pass, $command, $folder="INBOX")
        {   //Send an imap command directly to the imap server

            $result=["response"=>"", "error"=>""];
            $url = "imaps://$server/". rawurlencode($folder);
            $options=[
                CURLOPT_URL=>$url,
                CURLOPT_PORT=> 993,
                CURLOPT_USERNAME=> $user,
                CURLOPT_PASSWORD=> $pass, 
                CURLOPT_RETURNTRANSFER=> true, 
                CURLOPT_HEADER=> true,
                CURLOPT_CUSTOMREQUEST=> $command
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);

            $result["response"] = curl_exec($ch);
            if(curl_errno($ch)) $response["error"]="Error (". curl_errno($ch) ."): ". curl_error($ch);

            return $result;
        }

        public function send($criteria)
        {
            $exemple_criteria ='UID SEARCH SINCE "01-03-2023" (OR FROM "mailer-daemon" FROM "postmaster") (OR SUBJECT "fail" (OR SUBJECT "undeliver" SUBJECT "returned"))';
            //Pull out all the emails returned as undeliverable by the remote mail server in the inbox using curl
            $response=$this->send_imap_command(
                                $this->host,$this->user,
                                $this->pass,
                                $criteria,
                                $this->folder
                            );
            $messages = null;
            if($response["error"]!="")
            {
                return $response["error"]."\n";
                
            } 
            elseif (strlen($response["response"])>5)
            {
                //Server returns a string in the form * SEARCH uid1 uid2 uid3 ...  Clean up and create array of UIDs.
                $response["response"]=str_replace("* SEARCH ","",$response["response"]);
                $messages=explode(" ",$response["response"]);
            }
    
            return $messages;

        }


        
    }