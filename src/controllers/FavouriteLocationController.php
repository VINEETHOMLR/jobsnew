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


class FavouriteLocationController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->locationmdl = (new FavouriteLocation);
    }

    public function actionIndex(){

        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');  
        }
       
       
        $data = $this->locationmdl->getLocationDetail($userId);
        
        return $this->renderAPI($data, 'Location Data', 'false', 'S01', 'true', 200);
        

    }

      public function actionEditFavLocation(){

        $input   = $_POST;
        $userObj = Raise::$userObj;
        $location_name  = issetGet($input,'location_name','');
        $lat  = issetGet($input,'lat','');
        $longitude  = issetGet($input,'longitude','');
        $action  = issetGet($input,'action','');
        $id  = issetGet($input,'loc_id','');
        $userId  = $userObj['id'];

        
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');  
        }

        if(empty($action)) {
            return $this->renderAPIError(Raise::t('common','enter_action'),'');  
        }

        if(!in_array($action,[1,2])) {
            
            return $this->renderAPIError(Raise::t('common','invalid_action'),'');
        }


        if($action==1){      
           if(empty($location_name)) {
            return $this->renderAPIError(Raise::t('common','enter_loc_name'),'');  
           }
           if(empty($lat)) {
            return $this->renderAPIError(Raise::t('common','enter_lat'),'');  
           }
           if(empty($lat)) {
            return $this->renderAPIError(Raise::t('common','enter_valid_lat'),'');  
           }
           if(!is_numeric($longitude)) {
            return $this->renderAPIError(Raise::t('common','enter_valid_long'),'');  
           }
           if(!is_numeric($lat)) {
            return $this->renderAPIError(Raise::t('common','enter_valid_lat'),'');  
           }

           $input['user_id'] = $userId;
           $input['status'] = 1;

            // $data = $this->locationmdl->addLocation($userId);
            $data = $this->locationmdl->assignAttrs($input)->save();

        } else if($action==2){  
          
               if(empty($id)) {
                return $this->renderAPIError(Raise::t('common','enter_id'),'');  
               }
                if(!is_numeric($id)) {
                return $this->renderAPIError(Raise::t('common','enter_valid_id'),'');  
               }

                $data = $this->locationmdl->deleteLocation($id);
        } 

        $data = $this->locationmdl->getLocationDetail($userId);
        return $this->renderAPI($data, 'Location Action Completed', 'false', 'S01', 'true', 200);
        
    }


}

   

