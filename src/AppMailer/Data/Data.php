<?php
namespace App\AppMailer\Data;
    

    class Data
    {
        public $step = [
            [
                'name'=>'step1',
                'subject'=>'hello',
                'linkToEmail'=>'template/email154.html.twig'
            ],
            [
                'name'=>'step2',
                'subject'=>'bonjour',
                'linkToEmail'=>'template/email15254.html.twig'
            ],
            [
                'name'=>'step3',
                'subject'=>'last message',
                'linkToEmail'=>'template/email15254.html.twig'
            ]


        ];
        
        public $leads = [
            [
                'name'=>'koff azia',
                'emailAddress'=>'koffazia@gmail.com',
            ],
            [
                'name'=>'clemaos',
                'emailAddress'=>'clemaos@yahoo.com',
            ],
            [
                'name'=>'alice brunett',
                'emailAddress'=>'alice.brunett44@gmail.com',
            ],
            [
                'name'=>'clemaos ltd',
                'emailAddress'=>'contact@clemaos.com',
            ]
        ];

    }