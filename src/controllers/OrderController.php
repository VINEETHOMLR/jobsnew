<?php

namespace src\controllers;

use inc\Controller;
use inc\Raise;
use src\lib\Router;
use src\lib\Helper;
use src\lib\Secure;
use src\lib\RRedis;
use src\lib\ValidatorFactory;
use src\models\User;
use src\models\Jobs;
use src\models\Chat;




class OrderController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->jobs = (new Jobs);
        $this->chats = (new Chat);
    }

    public function actionOpenOrders()
    {

    	$userObj = Raise::$userObj;
    	$params  = [];
    	$params['user_id'] = $userObj['id'];
    	$orderList = $this->jobs->getOpenOrders($params);
        return $this->renderAPI($orderList, 'Success', 'false', 'S01', 'true', 200);




    }

    public function actionPastOrders()
    {

    	$userObj = Raise::$userObj;
    	$params  = [];
    	$params['user_id'] = $userObj['id'];
    	$orderList = $this->jobs->getPastOrders($params);
        return $this->renderAPI($orderList, 'Success', 'false', 'S01', 'true', 200);




    }

    
    


}
