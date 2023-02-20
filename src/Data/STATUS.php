<?php
    namespace App\Data;


    class STATUS
    {

        public const
            LEAD_COMPLETE = 'lead.complete',
            LEAD_STEP_1 = 'lead.step.1',
            LEAD_STEP_2 = 'lead.step.2',
            LEAD_STEP_3 = 'lead.step.3',
            LEAD_STEP_4 = 'lead.step.4',
            LEAD_STEP_5 = 'lead.step.5',
            LEAD_STEP_6 = 'lead.step.6',
            LEAD_STEP_7 = 'lead.step.7',
            LEAD_STEP_8 = 'lead.step.8',
            LEAD_STEP_9 = 'lead.step.9',
            LEAD_STEP_10 = 'lead.step.10',

            STEP_DRAFT  = 'step.draft',
            STEP_ONHOLD  = 'step.onhold',
            STEP_INPROGRESS  = 'step.inprogress',
            STEP_COMPLETE  = 'step.complete',

            COMPAIGN_DRAFT ='compain.draft',
            COMPAIGN_INPROGRESS ='compain.inprogress',
            COMPAIGN_COMPLETE ='compain.complete'
            ;


        public function __construct()
        {
    
        }

    }