<?php
    
    namespace App\Controller;

use App\Entity\Lead;
use App\Entity\Compaign;
use App\AppMailer\Data\STATUS;
use App\Repository\LeadRepository;
use Symfony\Component\Filesystem\Path;
use Doctrine\ORM\EntityManagerInterface;
use App\AppMailer\Sender\CompaignLuncher;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\AppMailer\Data\TransparentPixelResponse;
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
use Symfony\Component\HttpFoundation\File\File as FileFile;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

    class CompaignController extends AbstractController
    {

        public function __construct(
            private CrudControllerHelpers $crud,
            private SluggerInterface $slugger,
            private PaginatorInterface $paginator,
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
                $comp = $this->crud->addCompaign($this->getUser()->getId(),$name);
                $this->addFlash('success','compaign add');
                // return $this->redirectToRoute('app_compaign');
            }

            else $this->addFlash('warning','compaign name does not exist');


            return $this->redirectToRoute('app_compaign');

        }
        #[Route('/app/compaign/lunch/{id}',name:'app_compaign_lunch')]
        public function lunchCompaign(Compaign $compaign,CompaignLuncher $cl)
        {
            $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);
            $compaign->setStatus($Status);

            //appel le cron sur un lien lanceur
            //Quelques codes
            $res = [
                'response'=>true,
                'message'=> 'compaign lunched successfully'
            ];
            return $this->json($res);

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
        #[Route('/app/compaign/{id}/detail/{link?}',name:'app_compaign_detail')]
        public function compaignDetail(Compaign $compaign,Request $request, ?string $link)
        {

            $leadform = $this->getLeadForm($compaign->getId());
            $seqform = $request->request->all('seqform');


            return $this->render('Email/compaign_detail.html.twig',[
                'compaign'=> $compaign,
                'leadform'=>$leadform,
                'var'=>$seqform,
                'link' => $link
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
        public function compaignLeadDelete(Lead $lead)
        {
            $compaignId = $lead->getCompaign()->getId();
            if($lead != null) 
            {
                $this->crud->deleteLead($lead);
                $this->addFlash('success','lead deleted');
                
            }
            return $this->redirectToRoute('app_compaign_leads',['id'=>$compaignId]);
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
            return $this->redirectToRoute('app_compaign_detail',['id'=>$compaign->getId(),'link'=>"sequence"]);

            

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
                    if($data[0] === 'message') 
                    {
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

            return $this->redirectToRoute("app_compaign_detail",["id"=>$compaign->getId(),"link"=>"sequence"]);


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
        private function fileTreatment(UploadedFile $file)
        {
            if ($file)
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try 
                {
                    $file->move(
                        $this->getParameter('file_directory'),
                        $newFilename
                    );
                }
                catch (FileException $e)
                {
                    // ... handle exception if something happens during file upload
                }
            }

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
        




    }