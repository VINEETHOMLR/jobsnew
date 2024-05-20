<?php

namespace src\controllers;

use inc\Controller;
use inc\Raise;
use src\lib\Router;
use src\lib\Helper;
use src\lib\Secure;
use src\lib\RRedis;
use src\lib\ValidatorFactory;
//use src\models\User;
//use src\models\FavouriteLocation;


class TermaAndConditionsController extends Controller
{
    
    protected $needAuth = false;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        //$this->usermdl = (new User);
        //$this->locationmdl = (new FavouriteLocation);
    }

    public function actionIndex()
    { 

        echo "Terma And Conditions";

        //return $this->render('privacypolicy');
       

    }

    


}

   

