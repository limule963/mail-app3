<?php
    
    namespace App\Controller;

use App\Entity\Lead;
use App\Entity\Compaign;
use App\AppMailer\Data\STATUS;
use Doctrine\ORM\EntityManagerInterface;
use App\AppMailer\Sender\CompaignLuncher;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\AppMailer\Data\TransparentPixelResponse;
use App\Repository\LeadRepository;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            $name = $request->request->get('name');
            if($name != null)
            {
                $this->crud->addCompaign($this->getUser()->getId(),$name);
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
        #[Route('/app/compaign/{id}/detail',name:'app_compaign_detail')]
        public function compaignDetail(Compaign $compaign,Request $request)
        {
            $leadform = $this->getLeadForm();
            $seqform = $this->getSequenceForm();
            
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
            }

            return $this->render('Email/compaign_detail.html.twig',[
                'compaign'=> $compaign,
                'leadform'=>$leadform,
                'seqform'=>$seqform
            ]);
        }

        // #[Route(path:'/app/compaign/{id}/leads/add',name:'app_compaign_leads_add')]
        // public function compaignAddLeads(Compaign $compaign,Request $request)
        // {

        //     $compaignId = $compaign->getId();

        //     $form = $this->getUploadFileForm($compaignId);
            
        //     $form->handleRequest($request);
        //     if($form->isSubmitted() && $form->isValid())
        //     {
        //         $file = $form->get('leads')->getData();
                
        //         $textleads = $form->get('lead')->getData();

        //         if($textleads!=null) 
        //         {
        //             $this->crud->addLeadsByString($compaignId,$textleads);
        //             $this->addFlash('success','Leads Add successfully');

        //         }

        //         if($file!=null) 
        //         {
        //             $this->crud->addLeadsByfile($compaignId,$file);
        //             $this->addFlash('success','Leads Add successfully');
        //             $this->fileTreatment($file);

        //         }

        //         return $this->redirectToRoute('app_compaign_detail',['id'=>$compaignId,'link'=>'#sequence']);
        //     }
        // }






    
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


        #[Route(path:'/compaign/step/add/{id}',name:'app_compaign_step_add')]
        public function compaignStepAdd()
        {
            
        }
        


        

        private function getLeadForm()
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
            ->getForm() ;
        }

        private function getSequenceForm()
        {
            return $this->createFormBuilder()
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
        




    }