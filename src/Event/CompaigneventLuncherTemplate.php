<?php
    namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

    class CompaigneventLuncherTemplate extends Event
    {
        public $name;
        // private $compaignId;

        public function __construct(private int $compaignId,private $compaignName)
        {
            $this->name = $compaignName.$compaignId.'lunch';
        }
        public function getCompaignId()
        {
            return $this->compaignId;
        }
        public function getCompaignName()
        {
            return $this->compaignName;
        }
    }