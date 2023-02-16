<?php
    namespace App\Classes;
    class SimpleObject
    {
        public $name;
        public $priority;
        public function __construct($name,$priority)
        {
            $this->name =$name;
            $this->priority =$priority;
        }

        public function __toString()
        {
            return $this->priority;
        }
    }