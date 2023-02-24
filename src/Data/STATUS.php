<?php
    namespace App\Data;


    class STATUS
    {

        public const
            LEAD_COMPLETE = 'complete',


            STEP_DRAFT  = 'step.draft',
            STEP_1  = 'step.1',
            STEP_2  = 'step.2',
            STEP_3  = 'step.3',
            STEP_4  = 'step.4',
            STEP_5  = 'step.5',
            STEP_6  = 'step.6',
            STEP_7  = 'step.7',
            STEP_8  = 'step.8',
            STEP_9  = 'step.9',
            STEP_10  = 'step.10',

            COMPAIGN_DRAFT ='compaign.draft',
            COMPAIGN_INPROGRESS ='compaign.inprogress',
            COMPAIGN_COMPLETE ='compaign.complete',
            COMPAIGN_ACTIVE = 'compaign.active',
            COMPAIGN_PAUSED = 'compaign.paused',

            SEQUENCE_ONHOLD ='sequence.onhold',
            SEQUENCE_COMPLETE = 'sequence.complete'
            ;


        public function __construct()
        {
    
        }

    }