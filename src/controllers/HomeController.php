<?php

namespace src\controllers;

use inc\Controller;
use inc\Raise;
use src\lib\Router;
use src\lib\Helper;
use src\lib\Secure;
use src\lib\RRedis;
use src\lib\ValidatorFactory;
use src\models\Category;
use src\models\Notification;

class HomeController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = ['GetProducts'];

    public function __construct()
    {
        parent::__construct();
        $this->mdl = (new Category);
    }

 

    public function actionCategoryList()
    {

        

        $userObj        = Raise::$userObj;
        $user_id        = $userObj['id'];
        //$input          = $_POST;



        if(empty($user_id)) 
        {
            $this->renderAPIError("Invalid User");
        }


        $params = [];


        $List  = (new Category)->getCategory();
        $FavList  = (new Category)->getFavCategory();

        
        $data['AllCategory']        = $List;
        $data['popularcategory']    = $FavList;
        $status = 'true';
        $show_alert = 'false';
        $code = 'S12';
        return $this->renderAPI($data, "Category List", $show_alert, $code, $status, 200);


    }

    public function actionNotificationList()
    {
        

        $userObj        = Raise::$userObj;
        $user_id        = $userObj['id'];
        //$input          = $_POST;

        if(empty($user_id)) 
        {
            $this->renderAPIError("Invalid User");
        }

        $params = ['user_id'=>$user_id];

        $List  = (new Notification)->getNotifications($params);
       
        $status = 'true';
        $show_alert = 'false';
        $code = 'S12';
        return $this->renderAPI($List, "Notification List", $show_alert, $code, $status, 200);


    }

    public function actionAllCategoryList()
    {

        

        $userObj        = Raise::$userObj;
        $user_id        = $userObj['id'];
        $role           = $userObj['role_id'];
        //$input          = $_POST;



        if(empty($user_id)) 
        {
            $this->renderAPIError("Invalid User");
        }

        if($role!=2) 
        {
            $this->renderAPIError("Invalid Role");
        }

        $params = [];


        $List  = (new Category)->getAllCategory();
        

      
        
        $status = 'true';
        $show_alert = 'false';
        $code = 'S17';
        return $this->renderAPI($List, "All Category List", $show_alert, $code, $status, 200);


    }
    
}
