<?php

    

    namespace App\Controller;



use App\Entity\Dsn;

use App\Entity\Lead;

use App\Entity\Step;

use DateTimeImmutable;

use App\Entity\Compaign;

use App\AppMailer\Data\STATUS;

use App\Repository\LeadRepository;

use App\AppMailer\Sender\CompaignLuncher;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;

use App\AppMailer\Receiver\BulkCompaignMailSaver;

use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Serializer\Encoder\CsvEncoder;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Serializer\Encoder\DecoderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\File\Exception\FileException;



    class CompaignController extends AbstractController

    {



        public function __construct(

            private CrudControllerHelpers $crud,

            private SluggerInterface $slugger,

            private PaginatorInterface $paginator,

            private BulkCompaignMailSaver $bcms

            )

        {

            

        }

        

        #[Route('app/compaign',name:'app_compaign')]

        public function index()

        {

            

            return $this->render('Email/compaign.html.twig',[]);

        }





        #[Route('app/compaign/delete/{id}',name:'app_compaign_delete')]

        public function deleteCompaign(Compaign $compaign)

        {



                $this->crud->deleteCompaign($compaign);

                $this->addFlash('success', 'compaign deleted');

        



           return $this->redirectToRoute('app_compaign');

            



        }



        #[Route('app/compaign/add/',name:'app_compaign_add')]

        public function addCompaign(Request $request)

        {

            $name = $request->request->get('compaign_name');

            if($name != null)

            {

                $this->crud->addCompaign($this->getUser()->getId(),$name);

                $this->addFlash('success','compaign add');

                // return $this->redirectToRoute('app_compaign');

            }



            else $this->addFlash('warning','compaign name does not exist');





            return $this->redirectToRoute('app_compaign');



        }

        #[Route('/app/compaign/{id}/lunch',name:'app_compaign_lunch')]

        public function lunchCompaign(Compaign $compaign,CompaignLuncher $cl)

        {



            if($compaign->getStatus()->getStatus() === STATUS::COMPAIGN_COMPLETE)

            {

                $this->addFlash("warning","Compaign complete");

                $this->crud->saveCompaign($compaign);

                return $this->redirectToRoute("app_compaign");

            }

            else if($compaign->getStatus()->getStatus() === STATUS::COMPAIGN_DRAFT)

            {

                $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);

                

                $dsns = $compaign->getDsns()->getValues();

                $leads = $compaign->getLeads()->getValues();

                $steps = $compaign->getSteps()->getValues();

                /**@var Step */

                $step1 = $steps[0];

                $email1 = $step1->getEmail();

                

                if($dsns == null || $leads == null || $email1->getSubject() == null || $email1->getTextMessage() == null)

                {

                    $this->addFlash("warning","Compaign  Not Configured");

                    return $this->redirectToRoute("app_compaign");



                }



                

                $compaign->setStatus($Status);

                $this->crud->saveCompaign($compaign);



                return $this->redirectToRoute("app_compaign");



            }

            else if($compaign->getStatus()->getStatus() === STATUS::COMPAIGN_ACTIVE)

            {

                $Status = $this->crud->getStatus(STATUS::COMPAIGN_PAUSED);

                $compaign->setStatus($Status);

                $this->crud->saveCompaign($compaign);



                return $this->redirectToRoute("app_compaign");                



            }

            else if($compaign->getStatus()->getStatus() === STATUS::COMPAIGN_PAUSED)

            {

                $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);

                $compaign->setStatus($Status);

                $this->crud->saveCompaign($compaign);

                

                return $this->redirectToRoute("app_compaign");                



            }

            else return $this->redirectToRoute("app_compaign");  

            

            // $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);

            // $compaign->setStatus($Status);



            //appel le cron sur un lien lanceur

            //Quelques codes



            // $res = [

            //     'response'=>true,

            //     'message'=> 'compaign lunched successfully'

            // ];



            // return $this->json($res);



        }

        



        #[Route('/app/compaign/pause/{id}',name:'app_compaign_pause')]

        public function pauseCompaign(Compaign $compaign,CompaignLuncher $cl)

        {

            $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);

            $compaign->setStatus($Status);



            //appel le cron sur un lien lanceur

            //Quelques codes

            $res = [

                'response'=>true,

                'message'=> 'compaign paused successfully'

            ];

            return $this->json($res);



        }

        #[Route('/app/compaign/{id}/detail/{link?}-{link2?}',name:'app_compaign_detail')]

        public function compaignDetail(Compaign $compaign,Request $request, ?string $link,?string $link2)

        {

            $leadform = $this->getLeadForm($compaign->getId());

            $seqform = $request->request->all('seqform');





            return $this->render('Email/compaign_detail.html.twig',[

                'compaign'=> $compaign,

                'leadform'=>$leadform,

                'var'=>$seqform,

                'link' => $link,

                'link2'=>$link2

            ]);

        }



        #[Route(path:'/app/compaign/{id}/leads/add',name:'app_compaign_leads_add')]

        public function compaignAddLeads(Compaign $compaign,Request $request)

        {

            // dd('am in');

            $leadform = $this->getLeadForm($compaign->getId());

            $leadform->handleRequest($request);

            if($leadform->isSubmitted() && $leadform->isValid())

            {

                

                $file = $leadform->get('leads')->getData();

                $textleads = $leadform->get('lead')->getData();



                if($textleads!=null) 

                {

                    $this->crud->addLeadsByString($compaign->getId(),$textleads);

                    $this->addFlash('success','Leads Add successfully');

                   

                }



                if($file!=null) 

                {

                    $this->crud->addLeadsByfile($compaign->getId(),$file);

                    $this->addFlash('success','Leads Add successfully');

                    $this->fileTreatment($file);

                }

                return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId()]);

            }

            $this->addFlash("warning","No lead to add");

            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId()]);



        }













    

        #[Route(path:'/app/compaign/{id}/leads',name:'app_compaign_leads')]

        public function compaignLead($id,LeadRepository $rep,Request $request )

        {

            $begin = 0;

            $max = 10;

            $page = $request->query->getInt('page');



            if($page!=0) $begin = ($page - 1) * $max;

            



            $leads = $this->paginator->paginate(

                $rep->findforPag($id), /* query NOT result */

                $request->query->getInt('page', 1), /*page number*/

                $max /*limit per page*/

            );



            return $this->render('Email/compaign_leads.html.twig',[

                'leads'=>$leads,

                'begin'=>$begin,

                'id'=>$id

            ]);

        }





        #[Route(path:'/app/compaign/leads/delete/{id}',name:'app_compaign_leads_delete')]

        public function compaignLeadDelete(Lead $lead,Request $request)

        {

            $submittedToken = $request->request->get("token");

            $compaign = $lead->getCompaign();

            

            if($this->isCsrfTokenValid('delete_lead',$submittedToken))

            {

                

                

                $compaign->removeLead($lead);

                $this->crud->saveCompaign($compaign);

                $this->addFlash('success','lead deleted');

            }

            else $this->addFlash("warning", "Not autorise");

                

            

            return $this->redirectToRoute('app_compaign_leads',['id'=>$compaign->getId()]);

        }



        

        #[Route(path:'/compaign/step/delete/{id}',name:'app_compaign_step_delete')]

        public function compaignStepDelete(Step $step)

        {





            $compaign = $step->getCompaign();

            $compaignId = $compaign->getId();

            $link = "sequence";

            $link2 = $step->getId();    

            $compaign->removeStep($step);

            $this->crud->saveCompaign($compaign);

            // $this->crud->deleteStep($step->getId());

            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaignId,"link"=>$link]);

            

        }





        #[Route(path:'/compaign/{id}/step/add',name:'app_compaign_step_add')]

        public function compaignStepAdd(Compaign $compaign, Request $request)

        {

            $name = $request->request->get('step_name');



            if($name != null)

            {



                $step = $this->crud->createStep($name,'','');

                $compaign->addStep($step);

                $this->crud->saveCompaign($compaign);

                $this->addFlash("success","step add");





            }

            return $this->redirectToRoute('app_compaign_detail',['id'=>$compaign->getId(),'link'=>"sequence","link2"=>$step->getId()]);



            



        }



        #[Route(path:'/compaign/{id}/step/update',name:'app_compaign_step_update')]

        public function compaignStepUpdate(Compaign $compaign, Request $request)

        {

            $seqform = $request->request->all('seqform');

            $submittedToken = $request->request->get('token');

            



            if(empty($seqform))

            {

                $this->addFlash("warning", "Nothing to save");

                return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),"link"=>"sequence"]);

            }



            if ($this->isCsrfTokenValid('sequence', $submittedToken)) {

                

                $data = explode("_",array_key_first($seqform));

                $id = intval($data[1]);

                $step = $this->crud->getStep($id);

                $email = $step->getEmail();

                

                foreach($seqform as $key => $value)

                {

                    $data = explode("_",$key);

                    if($data[0] === 'dayAfter') $step->dayAfterLastStep = $value;

                    if($data[0] === 'subject') $email->setSubject($value);

                    if($data[0] === 'stepName') $step->setName($value);

                    if($data[0] === 'trackingLink') $email->setTrackingLink($value);

                    if($data[0] === 'message') 

                    {



                        //Correction for message and add tracking link if active





                        

                        //

                        $email->setTextMessage($value);



                        $path = $this->getParameter('sendmail_file_directory');

                        $name = "message_".$email->uid."_".$id.".html.twig";

                        $filename = $path.$name;

                        $this->createfile($name,$path);

                        

                        file_put_contents($filename,$value."\r\n\r\n{% endblock %}",FILE_APPEND);

                        $email->setEmailLink($name);



                    }



                

                    

                }





                $step->setEmail($email);

                $this->crud->saveStep($step);

                $this->addFlash('success','saved');

            }



            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),"link"=>"sequence","link2"=>$step->getId()]);





        }

        #[Route(path:'app/compaign/{id}/schedule/update',name:"app_compaign_schedule_update")]

        public function compaignScheduleUpdate(Compaign $compaign,Request $request)

        {

            $scheform = $request->request->all("scheform");

            $schedule = $compaign->getSchedule();

            $schedule->setFromm($scheform['from'])->setToo($scheform['to'])->setStartTime(new DateTimeImmutable($scheform['startTime'])) ;

            $compaign->setSchedule($schedule);



            $this->crud->saveCompaign($compaign);

            $this->addFlash("success","saved");



            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),"link"=>"schedule"]);



        }



        #[Route(path:"/app/compaign/{id}/config",name:"app_compaign_config")]

        public function compaignConfig(Compaign $compaign,Request $request)

        {

            $optform = $request->request->all("options");

            $checkform = $request->request->all("optionscheck");



            foreach($checkform as $id)

            {

                $dsn = $this->crud->getUserDsn($this->getUser()->getId(),$id);

                $compaign->addDsn($dsn);

                $this->crud->saveCompaign($compaign,false);

            }



            foreach($optform as $key=> $value )

            {

                if($key == "newStepPriority")

                {

                    $compaign->newStepPriority = intval($value);

                    $this->crud->saveCompaign($compaign,false);

                }



                if($key == "tracker")

                {

                    $compaign->setIsTracker(intval($value));

                    $this->crud->saveCompaign($compaign,false);



                }

            }

            

            $this->crud->em->flush();

            $this->addFlash("success", "Saved");

            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),"link"=>'options']);

        }



        #[Route(path:"/app/compaign/{id}/dsn/delete/{id2}",name:"app_compaign_dsn_delete")]

        public function compaignDsnsAdd(Compaign $compaign, 

                #[MapEntity(expr: 'repository.find(id2)')]

                Dsn $dsn)

        {

            // $compaign = $dsn->getCompaigns();

            $compaign->removeDsn($dsn);



            $this->crud->saveCompaign($compaign);

            $this->addFlash("success","Email removed");



            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),'link'=>'options']);

        }

        

        

        #[Route(path:"/app/compaign/mail/attachment",name:"app_compaign_mail_attachment")]

        public function storeAttachment(Request $request)

        {

            $key = $request->request->get('key');



            // if($key)$this->crud->addCompaign($this->getUser()->getId(),$key);

            // dd("dne");



            $content_type = $request->request->get('Content-Type');



            /**@var UploadedFile */

            $file = $request->files->get('file');



            $filename ='';

            if($file)

            {

                $filename = $this->fileTreatment($file,'mail_attachment_directory',$key);

            }



            

            // return $this->json(["url"=>'https://localhost:8000/assets/images/mail/'.$filename,"href"=>'https://localhost:8000/assets/images/mail/'.$filename]);

            return $this->json([],204);

        }



        

        





        private function myHandleRequest(Request $request)

        {   

            return  $request->$request->get('seqform');

            



        }



        private function getLeadForm($compaignId)

        {

            return $this->createFormBuilder()

            ->add('leads',FileType::class,[

                    'label' => 'Upload Leads (csv,json,xml)',

                    'mapped'=> false,

                    'required' => false,

                    'constraints'=>[

                        new File([

                            'maxSize' => '1024k',

                            'mimeTypes' => [

                                'text/csv',

                                'application/json',

                                'application/xml'

                            ],

                            'mimeTypesMessage' => 'Please upload a valid  document',                                

                        ])

                    ],

                ])

            ->add('lead',TextareaType::class,[

                    'mapped'=>false,

                    'required'=>false,

                    'label' => 'Add leads by text',

                    'attr'=>[

                        'placeholder'=>"name,email\nname,email\nname,email",

                        'rows'       =>"5"

                    ]

                    

                ])

            ->add('submit',SubmitType::class,['label'=>'Add'])

            ->setAction($this->generateUrl("app_compaign_leads_add",["id"=>$compaignId]))

            ->getForm() ;

        }



        private function getSequenceForm()

        {

            return $this->createFormBuilder(null,[])

                ->add('subject',TextType::class,[

                    'required'=>true

                ])

                ->add('message',TextType::class,[

                    'required'=>true

                ])

                ->add('dayAfter',NumberType::class,[

                    'required'=>true



                ])

                ->getForm();

        }



        private function paginator($id,$rep, Request $request)

        {

            $leads = $this->paginator->paginate(

                $rep->findforPag($id), /* query NOT result */

                $request->query->getInt('page', 1), /*page number*/

                10 /*limit per page*/

            );

        }



        private function deserialize(UploadedFile $file)

        {

            $od = new ObjectNormalizer();

            $decoders = [

                'csv'=>new CsvEncoder(),

                'json'=>new JsonEncoder(),

                'xml'=>new XmlEncoder()

            ];



            $ext = $file->guessExtension();

            $data = $file->getContent();



            /**@var DecoderInterface  */

            $dec = $decoders[$ext];



            $leads = $dec->decode($data,$ext);

            $leadsOb = null;

            

            

            foreach($leads as $lead)

            {

                $leadsOb[] = $od->denormalize($lead,Lead::class);

            }



            return $leadsOb;











        }

        private function fileTreatment(UploadedFile $file,$directory = 'file_directory',$filename = "")

        {

            $newFilename = '';

            if ($file)

            {

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL

                $safeFilename = $this->slugger->slug($originalFilename);

                if($filename === "") $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                else $newFilename = $filename;

                // Move the file to the directory where brochures are stored

                try 

                {

                    $file->move(

                        $this->getParameter($directory),

                        $newFilename

                    );

                }

                catch (FileException $e)

                {

                    // ... handle exception if something happens during file upload

                }

            }

            return $newFilename;



        }



        private function createfile(string $name,string $path)

        {

            $filename = $path.$name;

            $file = fopen($filename,"a+");

            if($file) 

            {

                $content = file_get_contents($path.'mailtemplate.html.twig');

                file_put_contents($filename,$content);

                // fseek($file,25);

                fclose($file);

                return true;

            }

            else  return false;

            

        }

        private function createfile2(string $filename,string $path)

        {

            $filesystem = new Filesystem();



            try 

            {

                // $filesystem->mkdir(

                //     Path::normalize(sys_get_temp_dir().'/'.random_int(0, 1000)),

                //);

                $filesystem->dumpFile($path.$filename,'{% extends "mail/send/base.html.twig" %}');

                // $filesystem->touch($path.$filename);

            } 

            catch (IOExceptionInterface $exception) 

            {

                echo "An error occurred while creating your directory at ".$exception->getPath();

            }



            

        }



        private function file_put($filename,$content)

        {

            $file = fopen($filename,'w+');

            fseek($file,20);

            fwrite($file,$content);

            fclose($file);



        }



        private function test(string $path)

        {

            return $path;

        }



        private function getAllMessages()

        {

            $user = $this->getUser();

            $compaigns = $user->getCompaigns()->getValues();

            $this->bcms->saveAll($compaigns);

        }

        









    }