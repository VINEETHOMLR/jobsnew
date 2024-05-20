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
use src\models\FavouriteLocation;


class AnnouncementController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->locationmdl = (new FavouriteLocation);
    }

    public function actionIndex()
    {

        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');  
        }

        $announcement = $this->usermdl->callsql("SELECT content,url FROM `announcement` WHERE status='1'","row");
        $data['announcement'] = html_entity_decode($announcement['content']);
        $data['url'] = ($announcement['url']);
        return $this->renderAPI($data, 'Announcement', 'false', 'S01', 'true', 200);
       

    }

    


}

   

