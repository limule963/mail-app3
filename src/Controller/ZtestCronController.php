<?php
    namespace App\Controller;

use App\AppMailer\Crontab;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

    class ZtestCronController extends AbstractController
    {

        #[Route(path:'/zcron',name:'app_zcron')]
        public function test()
        {
            // phpinfo();
            // $succes = putenv('PATH=/usr/bin');
            $output = shell_exec('crontab -l');
            $output = `crontab -l`;
            $pp = shell_exec('ls');
            dd($succes,$pp,$output);
            
            // Crontab::addJob('* * * * * curl https://app.clemaos.com/compaign/user/1/lunch');
            // $jobs = Crontab::getJobs();
            

            return $this->json($output);
        }
    }