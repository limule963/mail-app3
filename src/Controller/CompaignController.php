<?php
    
    namespace App\Controller;

use App\Entity\Lead;
use App\Entity\Compaign;
use App\AppMailer\Data\STATUS;
use App\AppMailer\Sender\CompaignLuncher;
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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

    class CompaignController extends AbstractController
    {

        public function __construct(private CrudControllerHelpers $crud,private SluggerInterface $slugger)
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
        #[Route('/app/compaign/detail/{id}',name:'app_compaign_detail')]
        public function compaignDetail(Compaign $compaign,Request $request)
        {

            $form = $this->getUploadFileForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $file = $form->get('leads')->getData();

                $objfile = $this->deserialize($file);

                $this->crud->updateCompaign(id: $compaign->getId(), leads: $objfile);
                
                $this->addFlash('success','Leads Add successfully');

                $this->fileTreatment($file);

            }
                    
                   

            return $this->render('Email/compaign_detail.html.twig',[
                'compaign'=> $compaign,
                'form'=>$form
            ]);
        }

        private function getUploadFileForm()
        {
            return $this->createFormBuilder()
            ->add('leads',FileType::class,[
                    'label' => 'Upload Leads (csv,json,xml)',
                    'mapped'=> false,
                    'required' => true,
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
                ->add('submit',SubmitType::class,['label'=>'Add'])
                ->getForm() ;
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