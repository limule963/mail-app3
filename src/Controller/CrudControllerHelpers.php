<?php
namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\User;
use App\Entity\Email;
use App\Entity\Status;
use App\Entity\Compaign;
use App\Entity\Schedule;
use App\AppMailer\Data\STATUS as STAT;
use App\Repository\DsnRepository;
use App\Repository\LeadRepository;
use App\Repository\StepRepository;
use App\Repository\UserRepository;
use App\Repository\CompaignRepository;
use App\Repository\ScheduleRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\AppMAiler\Exceptions\CrudControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    

    class CrudControllerHelpers extends AbstractController
    {
        public $em;
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

        public function updateUser($userId, $name = null,$email = null ,$password= null,Dsn $dsn = null,)
        {
            /**@var UserRepository */
            $rep = $this->em->getRepository(User::class);
            $user = $rep->find($userId); 
            if($dsn!=null) $user->addDsn($dsn);
            $rep->save($user,true);
        }





//---------------------------------------------------------------------
//               COMPAIGN_CRUD                                         |
//---------------------------------------------------------------------

        public function createCompaign($name)
        {
            $status = $this->getStatus(STAT::COMPAIGN_DRAFT);
            
            $sch = (new Schedule)->setStartTime(new \DateTimeImmutable())->setFromm(8)->setToo(18);


            return (new Compaign)->setName($name)->setStatus($status)->setSchedule($sch);

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
        public function updateCompaign($id,$name = null,  mixed $dsns = null, Step $step = null, Status $status =null,array $leads =null, Schedule $schedule =null)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            
            /**@var Compaign */
            $compaign = $rep->find($id);
            if($name!=null) $compaign->setName($name);
            if($dsns instanceof Dsn && $dsns != null) $compaign->addDsn($dsns);
            else
            {
                if($dsns != null) $compaign->addDsns($dsns);
            }
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

        public function getCompaigns($number)
        {
            /**@var User */
            $user = $this->user();
            $id = $user->getId();
            
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            
            return $rep->findByUserId($id,$number);
        }
        
        public function getCompaign()
        {
            /**@var User */
            $user = $this->user();
            $id = $user->getId();

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);

            $c = $rep->findByUserId($id,1);
            return $c[0];
        }


//---------------------------------------------------------------------
//               LEAD_CRUD                                             |
//---------------------------------------------------------------------






        public function createLead($name,$emailAddress,$status = STAT::STEP_1):Lead
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
            
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign =$rep->find($compaignId);
            
            if($leads instanceof Lead)$compaign->addLead($leads);
            else 
            {
                $compaign->addLeads($leads);
                $leads = $this->createLeads($leads);
            }
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

        public function getLeadsByStatus($compaignId,$status,$n)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            return $rep->findByStatus($compaignId,$status,$n);
        }
        public function getLeadByStatus($compaignId,$status):Lead|null
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $leads = $rep->findByStatus($compaignId,$status,1);
            if($leads==null) return null;
            else return $leads[0];

        }

        public function getLeads($compaignId)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            return $rep->findByCompainId($compaignId);           
        }
        public function getLead($compaignId)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $leads = $rep->findByCompaignId($compaignId,1);
            return $leads[0];
        }

        public function getLeadBySender($compaignId,$leadStatus,$sender):Lead|null
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);      
            $leads = $rep->findBySender($compaignId,$leadStatus,$sender,1);
            if( $leads == null) return null;
            else  return $leads[0];
        }
        
        public function getLeadsBySender($compaignId,$leadStatus,$sender,$number)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);      
            return $rep->findBySender($compaignId,$leadStatus,$sender,$number);
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

            $this->leadStatusOrding($compaignId);

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

        public function leadStatusOrding($compaignId)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);
            $steps = $rep->findByCompaignId($compaignId);
            // $count =count($steps);
            if(!empty($steps))
            {
                foreach ($steps as $key => $step) {
                    $key2 = $key+1;
                    $status = 'step.'.$key2;
                    $Status = $this->getStatus($status);
                    $step->setStatus($Status);
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
            return (new Schedule())->setFromm($from)->setToo($to)->setStartTime($startTime);
        }

        public function addSchedule(int $from,int $to,\DateTimeInterface $startTime,$compaignId)
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

//---------------------------------------------------------------------
//               DSN CRUD                                             |
//---------------------------------------------------------------------





















         
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


//---------------------------------------------------------------------
//               DSN_CRUD                                            |
//---------------------------------------------------------------------

        public function createDsn($email,$username,$password,$host,$port)
        {
            // /**@var DsnRepository */
            // $rep = $this->em->getRepository(Dsn::class);
            return (new Dsn)->setEmail($email)->setUsername($username)
                            ->setPassword($password)->setHost($host)
                            ->setPort($port)
                            ;
        }

        public function addDsnToCompaign($compaignId, mixed $dsnData)
        {
            if(!$dsnData instanceof Dsn) $dsn = $this->createDsn($dsnData['email'],$dsnData['password'],$dsnData['email'],$dsnData['host'],$dsnData['port']);
            $dsn = $dsnData;
            $this->updateCompaign($compaignId,dsns:$dsn);
        }

        public function getCompaignDsns($compaignId,$number = 50)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);
            return $rep->findByCompaignId($compaignId,$number);
        }
        public function getUserDsns($number = 50)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);

            /**@var User */
            $user = $this->getUser();
            $userId = $user->getId();
            return $rep->findByUserId($userId,$number);
        }

        public function addDsnToUser($dsnData)
        {
            if(!$dsnData instanceof Dsn) $dsn = $this->createDsn($dsnData['email'],$dsnData['password'],$dsnData['email'],$dsnData['host'],$dsnData['port']);
            $dsn = $dsnData;
            /**@var User */
            $user = $this->getUser();
            $this->updateUser(userId:$user->getId(), dsn:$dsn);
        }



    }