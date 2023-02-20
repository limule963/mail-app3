<?php
namespace App\Controller;

use App\Classes\Exception\CrudControllerException;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\User;
use App\Entity\Email;
use App\Entity\Status;
use App\Entity\Compaign;
use App\Data\STATUS as STAT;
use App\Repository\LeadRepository;
use App\Repository\StepRepository;
use App\Repository\UserRepository;
use App\Repository\CompaignRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    

    class CrudControllerHelpers extends AbstractController
    {
        private $em;
        private $comprep;
        private $steprep;
        private $leadrep;
        private $userrep;

        public function __construct(private UserPasswordHasherInterface $hasher,private ManagerRegistry $doc)
        {
            $this->em = $doc->getManager();
        }
        
        
        /**
         * Creation Crud
         */
        public function getStatus($status):Status
        {
            $Status = $this->isStatusExist($status);
            if($Status != null) return $Status;
            return (new Status)->setStatus($status);
           
        }

      

        public function createEmail($subject,$linkToEmail):Email
        {
            return (new Email)->setSubject($subject)->setEmailLink($linkToEmail);
        }



        public function createLead($name,$emailAddress,$status = STAT::LEAD_STEP_1):Lead
        {
            $sta = $this->getStatus($status);
            return (new Lead)->setStatus($sta)->setName($name)->setEmailAddress($emailAddress);
        }



        public function createLeads(array $leadsData,$status = STAT::LEAD_STEP_1):array
        {
            $sta = $this->getStatus($status);
            $leads = null;
            foreach($leadsData as $lead)
            {
                $leads[]= (new Lead)->setStatus($sta)
                                    ->setName($lead['name'])
                                    ->setEmailAddress($lead['emailAddress'])
                ;

            }
            return $leads;
        }




        public function createStep($name,$subject,$linkToEmail):Step
        {
            $status = $this->getStatus(STAT::STEP_DRAFT);
            $email = $this->createEmail($subject,$linkToEmail);
            return (new Step)
                ->setName($name)
                ->setStatus($status)
                ->setEmail($email)
                ;
        }
        // public function createStep($name,$subject,$linkToEmail):Step
        // {
        //     $status = $this->getStatus(STAT::STEP_DRAFT);
        //     $email = $this->createEmail($subject,$linkToEmail);
        //     return (new Step)
        //         ->setName($name)
        //         ->setStatus($status)
        //         ->setEmail($email)
        //         ;
        // }




        public function createSteps(array $steps):array
        {
            $newStep = null;
            foreach($steps as $step)
            {
                $newStep[] = $this->createStep($step['name'],$step['subject'],$step['linkToEmail']);

            }
            return $newStep;
        }


        
        
        // public function createCompaign($name,array $leads, array $steps)
        // {
        //     $status = $this->getStatus(STAT::COMPAIGN_DRAFT);
        //     $newsteps = $this->createSteps($steps);
        //     $newleads = $this->createLeads($leads);
        //     $compaign = (new Compaign)->setName($name)->setStatus($status);
        //     foreach($newleads as $lead)
        //     {
        //         $compaign->addLead($lead);
        //     }
        //     foreach($newsteps as $step)
        //     {
        //         $compaign->addStep($step);
        //     }
        //     return $compaign;
        // }
        public function createCompaign($name)
        {
            $status = $this->getStatus(STAT::COMPAIGN_DRAFT);

            $compaign = (new Compaign)->setName($name)->setStatus($status);

            return $compaign;
        }

        public function user()
        {
            if($this->getUser() == null) throw new CrudControllerException('User is not connected');
            return $this->getUser();
        }


        // public function createCompaigns(array $Compaigns)
        // {
        //     $newCompaigns = null;
        //     foreach($Compaigns as $compaign)
        //     {
        //         $newCompaigns[] = $this->createCompaign($compaign['name'],$compaign['leads'],$compaign['steps']);
        //     }

        //     return $newCompaigns;
        // }
//---------------------------------------------------------------------
//               COMPAIGN_CRUD                                         |
//---------------------------------------------------------------------
        public function addCompaign($name=null)
        {   
            $compaign = $this->createCompaign($name);

            /**@var User $user */
            $user = $this->user();
            $user->addCompaign($compaign);

            /**@var UserRepository */
            $rep = $this->em->getRepository(User::class);
            $rep->save($user,true);
            return $compaign->getId();
            
        }

        public function updateCompaign($id,$name)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            
            /**@var Compaign */
            $compaign = $rep->find($id);
            $compaign->setName($name);
            $rep->save($compaign,true);
            return $compaign->getId();
            
        }
        
        public function deleteCompaign($id)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign = $rep->find($id);
            $rep->remove($compaign,true);

        }

        public function getCompaigns()
        {
            /**@var User */
            $user = $this->user();
            $id = $user->getId();

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);

            return $rep->findByUserId($id);

           
        }


//---------------------------------------------------------------------
//               LEAD_CRUD                                             |
//---------------------------------------------------------------------
        public function addLeads($compaignId, mixed $leads)
        {
            $leads = $this->createLeads($leads);

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign =$rep->find($compaignId);
            $compaign->addLeads($leads);
            $rep->save($compaign,true);
        }

        public function updateLead($id,$name,$emailAddress)
        {   
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $lead = ($rep->find($id))->setName($name)->setEmailAddress($emailAddress);
            $rep->save($lead,true);
            
        }
        /**
         * For only system
         */
        public function save($lead)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $rep->save($lead,true);
            
        }

        public function getLeadsByStatus($status)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            return $rep->findByStatus($status);
        }
















//---------------------------------------------------------------------
//               STEP_CRUD                                             |
//---------------------------------------------------------------------
        public function addStep($compaignId,$name,$subject,$linkToEmail)
        {
            $step = $this->createStep($name,$subject,$linkToEmail);

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign = $rep->find($compaignId);
            $compaign->addStep($step);
            $rep->save($compaign,true);
        }

        public function updateStep($id,$name,$subject,$linkToEmail)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);

            /**@var Step */
            $step = $rep->find($id);
            $email = ($step->getEmail())->setSubject($subject)->setEmailLink($linkToEmail);
            $step->setName($name)->setEmail($email);
            $rep->save($step,true);
            
        }

        public function deleteStep($id)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);
            $step = $rep->find($id);
            $rep->remove($step,true);
        }

        public function getSteps($compaignId)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);

            return $rep->findByCompaignId($compaignId);
        }



        /**
         * Read crud
         */


        
























         
        private function isStatusExist($status)
        {
            /**
             * @var StatusRepository
             */
            $rep = $this->em->getRepository(Status::class);
            $Status = $rep->findOneBy(['status'=> $status]);
            if($Status != null) return $Status;
            else return null;
        }
    }