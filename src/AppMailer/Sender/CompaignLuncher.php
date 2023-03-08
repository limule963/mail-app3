<?php
namespace App\AppMailer\Sender;


use App\Controller\CrudControllerHelpers;
use App\AppMailer\Data\CompaignResponse;
use App\AppMailer\Data\Sequence;
use App\AppMailer\Data\STATUS;
use App\AppMailer\Receiver\AllFolderReceiver;
use App\AppMailer\Receiver\CompaignMailSaver;
use App\Entity\Compaign;

    class CompaignLuncher
    {

        private ?Sequence $sequence;
        private $compaignStatus;
        private $fromm;
        private $too;
        private $dsns;
        private $compaignId;
        
        public function __construct(private CompaignMailSaver $cms, private AllFolderReceiver $allrec,private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher,private CrudControllerHelpers $crud)
        {
            
        }
        
        public function sequence(Compaign $compaign):self
        {
            // $compaign->setStatus($this->crud->getStatus(STATUS::COMPAIGN_ACTIVE));
            $this->sequence = $this->sequencer->sequence($compaign);
            $this->compaignStatus = $compaign->getStatus()->getStatus();
            $this->dsns = $compaign->getDsns()->getValues();
            $this->compaignId = $compaign->getId();
            $this->fromm = $compaign->getSchedule()->getFromm();
            $this->too = $compaign->getSchedule()->getToo();
            return $this;
        }
        
        public function lunch()
        {

            
            if($this->compaignStatus != STATUS::COMPAIGN_ACTIVE) return $this->getStat();
            $date = getdate();
            $h = $date['hours'];
            if($h < $this->fromm && $h > $this->too) return $this->getStat();

            //synchro mails receve
            $this->cms->save($this->dsns,$this->compaignId,1);

            //lunch sequence
            return $this->sequenceLuncher->lunch($this->sequence);
        }

        private function getStat()
        {
            $cr = new CompaignResponse;
            return  $cr->setCompagneState($this->compaignStatus);
        }

        private function getMails()
        {
        }
    }