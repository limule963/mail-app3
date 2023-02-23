<?php
namespace App\Controller;

use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\User;
use App\Entity\Email;
use App\Entity\Status;
use App\Entity\Compaign;
use App\Entity\Schedule;
use App\Data\STATUS as STAT;
use App\Repository\LeadRepository;
use App\Repository\StepRepository;
use App\Repository\UserRepository;
use App\Repository\CompaignRepository;
use App\Repository\ScheduleRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Classes\Exception\CrudControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    

    class CrudControllerHelpers extends AbstractController
    {
        private $em;
        private $comprepo;
        private $steprepo;
        private $leadrepo;
        private $userrepo;
        private $scherepo;
        public function __construct(private UserPasswordHasherInterface $hasher,private ManagerRegistry $doc)
        {
            $this->em = $doc->getManager();
        }
        

//---------------------------------------------------------------------
//               STATUS_CRUD                                          |
//---------------------------------------------------------------------
        /**
         * Creation Crud
         */
        public function getStatus($status):Status
        {
            $Status = $this->isStatusExist($status);
            if($Status != null) return $Status;
            return (new Status)->setStatus($status);
           
        }

      

//---------------------------------------------------------------------
//               EMAIL_CRUD                                           |
//---------------------------------------------------------------------
        public function createEmail($subject,$linkToEmail):Email
        {
            return (new Email)->setSubject($subject)->setEmailLink($linkToEmail);
        }





//---------------------------------------------------------------------
//               USER_CRUD                                            |
//---------------------------------------------------------------------

        public function user()
        {
            if($this->getUser() == null) throw new CrudControllerException('User is not connected');
            return $this->getUser();
        }

        public function createUser()
        {
            
        }





//---------------------------------------------------------------------
//               COMPAIGN_CRUD                                         |
//---------------------------------------------------------------------

        public function createCompaign($name)
        {
            $status = $this->getStatus(STAT::COMPAIGN_DRAFT);
            return $compaign = (new Compaign)->setName($name)->setStatus($status);

        }

        public function addCompaign($name=null)
        {   
            $compaign = $this->createCompaign($name);

            /**@var User $user */
            $user = $this->user();
            $user->addCompaign($compaign);

            /**@var UserRepository */
            $rep = $this->em->getRepository(User::class);
            $rep->save($user,true);
        }

        /**
         * @param Dsn[] $dsns
         * @param string $name
         */
        public function updateCompaign($id,$name = null, array $dsns = null, Step $step = null, Status $status =null,array $leads =null, Schedule $schedule =null)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            
            /**@var Compaign */
            $compaign = $rep->find($id);
            if($name!=null) $compaign->setName($name);
            if(!empty($dsns)) $compaign->addDsns($dsns);
            if($status!=null) $compaign->setStatus($status);
            if($schedule!=null) $compaign->setSchedule($schedule);
            if($step!=null) $compaign->addStep($step);

            $rep->save($compaign,true);
        }

        public function saveCompaign(Compaign $compaign)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $rep->save($compaign,true);

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

        public function getCompaign($id)
        {
            /**@var User */
            $user = $this->user();

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);

            return $rep->findOneById($id);
        }


//---------------------------------------------------------------------
//               LEAD_CRUD                                             |
//---------------------------------------------------------------------






        public function createLead($name,$emailAddress,$status = STAT::LEAD_STEP_1):Lead
        {
            $sta = $this->getStatus($status);
            return (new Lead)->setStatus($sta)->setName($name)->setEmailAddress($emailAddress);
        }


        /**@return Lead[] */
        public function createLeads(array $leadsData):array
        {
            foreach($leadsData as $lead)
            {
                $leads[]= $this->createLead($lead['name'],$lead['emailAddress']);
            }
            return $leads;
        }

        public function addLeads($compaignId, mixed $leads)
        {
            $leads = $this->createLeads($leads);

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign =$rep->find($compaignId);
            $compaign->addLeads($leads);
            $rep->save($compaign,true);
        }

        public function updateLead($id,$name = null,$emailAddress = null,Status $status = null )
        {   
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $lead = $rep->find($id);
            if($name != null) $lead->setName($name);
            if($emailAddress != null) $lead->setEmailAddress($emailAddress);
            if($status != null) $lead->setStatus($status);
                    
            $rep->save($lead,true);
            
        }
        /**
         * For only system
         */
        public function saveLead($lead)
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

        public function getLeads($compaignId)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            return $rep->findByCompainId($compaignId);           
        }
















//---------------------------------------------------------------------
//               STEP_CRUD                                             |
//---------------------------------------------------------------------



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




        public function createSteps(array $steps):array
        {
            $newStep = null;
            foreach($steps as $step)
            {
                $newStep[] = $this->createStep($step['name'],$step['subject'],$step['linkToEmail']);

            }
            return $newStep;
        }

        public function addStep($compaignId,$name,$subject,$linkToEmail)
        {
            
            $step = $this->createStep($name,$subject,$linkToEmail);
            $this->updateCompaign($compaignId,null,null,$step);

            $this->leadStatusOrding();

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
        public function saveStep(Step $step)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);
            $rep->save($step,true);
        }
        /**@var Step[] $steps */

        public function leadStatusOrding()
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);
            $steps = $rep->findAll();
            // $count =count($steps);
            if($steps!=null)
            {
                foreach ($steps as $key => $step) {
                    $key2 = $key+1;
                    $step->leadStatus = 'lead.step.'.$key2;
                    $rep->save($step);
                    
                } 
                $this->em->flush();
            }
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



//---------------------------------------------------------------------
//               SCHEDULE CRUD                                        |
//---------------------------------------------------------------------


        public function getSchedule($compaignId):Schedule
        {
            /**@var ScheduleRepository */
            $rep = $this->em->getRepository(Schedule::class);
            return $rep->findOneByCompaignId($compaignId);

        }

        public function createSchedule($from,$to, \DateTimeInterface $startTime)
        {
            return (new Schedule())->setFrom($from)->setTo($to)->setStartTime($startTime);
        }

        public function addSchedule($from,$to,\DateTimeInterface $startTime,$compaignId)
        {
            $schedule = $this->createSchedule($from,$to,$startTime);
            $this-> updateCompaign($compaignId,null,null,null,null,null,$schedule);

        }
        public function saveSchedule(Schedule $schedule)
        {
            /**@var ScheduleRepository */
            $rep = $this->em->getRepository(Schedule::class);
            $rep->save($schedule,true);
        }























         
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