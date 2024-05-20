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




class CategoryController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->categorymdl = (new Category);
    }

    public function actionGetCategoryList(){

        $userObj = Raise::$userObj;
        $userId = $userObj['id'];

        if(empty($userId)) {
            
            return $this->renderAPIError(Raise::t('common','err_userid_required'),''); 
        }

        $language = Raise::$lang; 
        
        $language = empty($language)? 'en':$language;

        $categoryList = $this->categorymdl->getList($language);
  
        $data         = ['categoryList'=>$categoryList];
        return $this->renderAPI($data, 'Category List', 'false', '', 'true', 200); 


    }

   






  

    

  


}
