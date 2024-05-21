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
}
