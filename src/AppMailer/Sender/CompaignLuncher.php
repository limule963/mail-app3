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
        private \DateTimeImmutable $compaignStartTime;
        private ?Compaign $compaign;
        
        public function __construct(private CompaignMailSaver $cms, private AllFolderReceiver $allrec,private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher,private CrudControllerHelpers $crud)
        {
            
        }
        
        public function sequence(Compaign $compaign):self
        {
            
            // $compaign->setStatus($this->crud->getStatus(STATUS::COMPAIGN_ACTIVE));
            $this->compaign = $compaign;
            $this->sequence = $this->sequencer->sequence($compaign);
            $this->compaignStatus = $compaign->getStatus()->getStatus();
            $this->dsns = $compaign->getDsns()->getValues();
            $this->compaignId = $compaign->getId();
            $this->fromm = $compaign->getSchedule()->getFromm();
            $this->too = $compaign->getSchedule()->getToo();
            $this->compaignStartTime = $compaign->getCreateAt();
            return $this;
        }
        
        public function lunch()
        {
            
            
            if($this->compaignStatus != STATUS::COMPAIGN_ACTIVE) return $this->getStat();
            $date = getdate();
            $h = $date['hours'];

           
            if($this->fromm == 0) $this->fromm = 24;

            if($h < $this->fromm && $h > $this->too) return $this->getStat();
            //synchro mails receve
            $this->cms->save($this->dsns,$this->compaignId,1,$this->compaignStartTime);

            //lunch sequence
            $cr = $this->sequenceLuncher->lunch($this->sequence);
            
            $tms = $this->compaign->getTms()+$cr->getTms();
            $tmr = $this->compaign->getTmr()+$cr->getTmr();
            $this->compaign->setTms($tms)->setTmr($tmr);
            $this->crud->saveCompaign($this->compaign);
            
            return $cr;
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