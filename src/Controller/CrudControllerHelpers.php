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
use App\Entity\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\MailRepository;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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


        public function get_User($userId):User
        {
             /**@var UserRepository */
            $rep = $this->em->getRepository(User::class);  
            return $rep->find($userId); 
        }

        public function updateUser($userId,$email = null ,$password= null,Dsn $dsn = null,)
        {
            /**@var UserRepository */
            $rep = $this->em->getRepository(User::class);
            $user = $rep->find($userId); 

            if($dsn!=null) $user->addDsn($dsn);
            if($email!=null) $user->setEmail($email);
            if($password!=null)
            {
                $newpass = $this->hasher->hashPassword($user,$password);
                $user->setPassword($newpass);
            }

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

        public function addCompaign($userId,$name)
        {   
            $compaign = $this->createCompaign($name);


            $user = $this->get_User($userId);
            $user->addCompaign($compaign);
            /**@UserRepository */
            $rep= $this->em->getRepository(User::class);
            $rep->save($user,true);
            return $compaign;
        }

        /**
         * @param Dsn[] $dsns
         * @param string $name
         */
        public function updateCompaign($id,$name = null,  mixed $dsns = null, Step $step = null, Status $status =null,mixed $leads =null, Schedule $schedule =null)
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

            if($leads instanceof Lead && $leads!=null) $compaign->addLead($leads);
            else
            {
                if($leads!=null) $compaign->addLeads($leads);
            }

            $rep->save($compaign,true);
        }

        public function saveCompaign(Compaign $compaign)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $rep->save($compaign,true);

        }
        
        public function deleteCompaignBy($id)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);
            $compaign = $rep->find($id);
            $rep->remove($compaign,true);

        }

        public function deleteCompaign(Compaign $compaign)
        {
            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);      
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
        
        public function getCompaign($userId,$compaignId)
        {
            /**@var User */
            $user = $this->get_User($userId);
            $id = $user->getId();

            /**@var CompaignRepository */
            $rep = $this->em->getRepository(Compaign::class);

            $c = $rep->find($compaignId);
            return $c;
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
        public function addLeadsByfile($compaignId,$filename,$format)
        {
            $normalizer = new ObjectNormalizer();

            if($format=='csv') $encoder = new CsvEncoder();
            else if($format=='json') $encoder = new JsonEncoder();

            $data = file_get_contents($filename);
            $dataDecode = $encoder->decode($data,$format);
            foreach($dataDecode as $lead) 
            {
                $leadTemp=$normalizer->denormalize($lead,Lead::class);
                $leadTemp->setStatus($this->getStatus(STAT::STEP_1));
                $leads[] = $leadTemp;
            }

            dd($leads);
            $this->updateCompaign(id:$compaignId,leads:$leads);
            
        }
        

        public function updateLead($id,$name = null,$emailAddress = null,Status $status = null, Mail $mail = null )
        {   
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $lead = $rep->find($id);
            if($name != null) $lead->setName($name);
            if($emailAddress != null) $lead->setEmailAddress($emailAddress);
            if($status != null) $lead->setStatus($status);

            if($mail != null) $lead->addMail($mail);
                    
            $rep->save($lead,true);
            
        }
        /**
         * For only system
         */
        public function saveLead($lead,bool $flush = true)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $rep->save($lead,$flush);
            
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
        public function getLeadsByCompaign($compaignId)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            $leads = $rep->findByCompaignId($compaignId,10);
            return $leads;
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
            return  $rep->findBySender($compaignId,$leadStatus,$sender,$number);
        }

        public function getLead($id)
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);  
            return $rep->find($id);  
        }

        public function getLeadByEmailAddress($compaignId,$emailAddress):?Lead
        {
            /**@var LeadRepository */
            $rep = $this->em->getRepository(Lead::class);
            return $rep->findOneByEmailAddress($compaignId,$emailAddress);
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
        public function saveStep(Step $step,bool $flush =true)
        {
            /**@var StepRepository */
            $rep = $this->em->getRepository(Step::class);
            $rep->save($step,$flush);
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
        public function saveSchedule(Schedule $schedule,bool $flush =true)
        {
            /**@var ScheduleRepository */
            $rep = $this->em->getRepository(Schedule::class);
            $rep->save($schedule,$flush);
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

        public function getUserDsns($userId,$number = 50)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);
            
            $user = $this->get_User($userId);
            $userId = $user->getId();
            return $rep->findByUserId($userId,$number);
        }
        public function getUserDsn($userId,$dsnId)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);

            $user = $this->get_User($userId);
            $userId = $user->getId();
            return $rep->find($dsnId);
        }

        public function addDsnToUser($dsnData)
        {
            if(!$dsnData instanceof Dsn) $dsn = $this->createDsn($dsnData['email'],$dsnData['password'],$dsnData['email'],$dsnData['host'],$dsnData['port']);
            $dsn = $dsnData;
            /**@var User */
            $user = $this->getUser();
            $this->updateUser(userId:$user->getId(), dsn:$dsn);
        }
        public function updateDsn($id,$mail = null)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);
            $dsn = $rep->find($id);
            if($mail!= null) $dsn->addMail($mail);
            $rep->save($dsn,true);
        }
        public function deleteDsn(Dsn $dsn)
        {
            /**@var DsnRepository */
            $rep = $this->em->getRepository(Dsn::class);
            $rep->remove($dsn,true);
        }
        



//---------------------------------------------------------------------
//               MAIL CRUD                                            |
//---------------------------------------------------------------------
        public function createMail(string $fromAddress,string $toAddress,string $date,int $mailId, string $folder,
                                    Lead $mailLead,Dsn $dsn,)
        {
            $mail = new Mail;
            return $mail->setMailId($mailId)->setFolder($folder)->setMailLead($mailLead)->setDsn($dsn);
        }
        public function addMail(mixed $mail)
        {
            /**@var MailRepository  */
            $rep = $this->em->getRepository(Mail::class);

            // if($mail instanceof Mail) $rep->save($mail,true);
            
            $mail =  $this->createMail($mail['fromAddress'],$mail['toAddress'],$mail['date'],$mail['mailId'],
                                        $mail['folder'],$mail['mailLead'],$mail['dsn']);
            $rep->save($mail,true);
            
        }

        public function saveMail(Mail $mail,bool $flush =true)
        {
            /**@var MailRepository  */
            $rep = $this->em->getRepository(Mail::class);
            $rep->save($mail,$flush);
        }

    }