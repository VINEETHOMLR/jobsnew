<?php

namespace src\controllers;

use inc\Controller;
use inc\Raise;
use src\lib\Router;
use src\lib\Helper;
use src\lib\Secure;
use src\lib\RRedis;
use src\lib\ValidatorFactory;
use src\models\Applications;
use src\models\Jobs;

class ApplicantListController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = ['GetProducts'];

    public function __construct()
    {
        parent::__construct();
        $this->mdl = (new Applications);

        $this->jobs = (new Jobs);
    }

 

    public function actionList()
    {

        

        $userObj        = Raise::$userObj;
        $user_id        = $userObj['id'];
        $input          = $_POST;


        $post_id        = issetGet($input,'post_id',"0");
        //$page           = issetGet($input,'page',"1");
        //$perPage        = issetGet($input,'perPage','10');
        $sort           = issetGet($input,'sort','');

        $sorts  = [];
        if($sort!="")
        {
            foreach($sort as $k=> $val)
            {
                if($val=='Price')
                {
                    $sorts[$k] = ' ap.basic_price ASC ';
                }
                else if($val=='Distance')
                {
                    $sorts[$k] = ' distance ASC ';
                }
                else if($val=='Rating')
                {
                    $sorts[$k] = '  ue.rating DESC ';
                }
                
            }
        }


        if(empty($user_id)) 
        {
            $this->renderAPIError("Invalid User");
        }

        if(empty($post_id)) 
        {
            $this->renderAPIError("Invalid Job");
        }

        $jobcheck = $this->mdl->callsql("SELECT `id`,`latitude`,`longitude`,`category_id` FROM job_post WHERE id=$post_id AND status IN (1,2) ",'row');

        if(empty($jobcheck))
            $this->renderAPIError("Invalid Job");

        $params = ['status' => '1','post_id' => $jobcheck['id'] ,'latitude' => $jobcheck['latitude'],'longitude' => $jobcheck['longitude'],'category_id' => $jobcheck['category_id']];



        $List  = (new Applications)->getRecords($params,$sorts);

        

        $status = 'true';
        $show_alert = 'false';
        $code = 'S11';
        return $this->renderAPI($List, "Applicant List", $show_alert, $code, $status, 200);


    }

    public function actionSavedApplicants()
    {

        
        $userObj        = Raise::$userObj;
        $user_id        = $userObj['id'];
        $input          = $_POST;

        //$page           = issetGet($input,'page',"1");
        //$perPage        = issetGet($input,'perPage','10');
        
        if(empty($user_id)) 
        {
            $this->renderAPIError("Invalid User");
        }


        $params = ['user_id' => $user_id];


        $List  = (new Applications)->getSavedRecords($params);


        $status = 'true';
        $show_alert = 'false';
        $code = 'S13';
        return $this->renderAPI($List, "Applicant Saved List", $show_alert, $code, $status, 200);


    }

public function actionMyOrder()
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

        $params = ['status' => '1','user_id' => $user_id ];

        $List  = $this->jobs->getMyOrders($params);

        
        $status = 'true';
        $show_alert = 'false';
        $code = 'S17';
        return $this->renderAPI($List, "My Orders", $show_alert, $code, $status, 200);


}
public function actionSaveApplicantData(){


        $input   = $_POST;
        //print_r($input);die();
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $role    = $userObj['role_id'];

        $post_id        = issetGet($input,'post_id','0');
        $applicant_id   = issetGet($input,'applicant_id','0');
        

        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }
        if($role!=1) {
            return $this->renderAPIError('Invalid user','');   
        }
        
        if(empty($post_id)) {
            return $this->renderAPIError("Invalid job",''); 
        }

        if(empty($applicant_id)) {
            return $this->renderAPIError("Invalid Applicant",''); 
        }

        $job = $this->mdl->callsql("SELECT COUNT(`id`) FROM job_post WHERE id=$post_id AND status IN (1,2) ",'value');

        if(empty($job)) {
            return $this->renderAPIError("Invalid Job",''); 
        }

        $applicant = $this->mdl->callsql("SELECT COUNT(`id`) FROM applications WHERE user_id='".$applicant_id."' AND status=1 AND post_id='".$post_id."' ",'value');

        if(empty($applicant)) {
            return $this->renderAPIError("Invalid Job",''); 
        }
    
        $params = [];
        $params['post_id']              = $post_id;
        $params['applicant_id']         = $applicant_id;
        $params['user_id']              = $userId;
        
        if($this->mdl->insertSavedApplicant($params))
        {
            return $this->renderAPI([], "Save applicant sucessfully", 'false', 'S21', 'true', 200); 
        }else{

            return $this->renderAPIError("Failed",'');    
        }
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 

}

public function actionReportApplicant(){


        $input   = $_POST;
        //print_r($input);die();
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $role    = $userObj['role_id'];

        $post_id        = issetGet($input,'post_id','0');
        $applicant_id   = issetGet($input,'applicant_id','0');
        $remark         = issetGet($input,'remark','');
        

        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }
        if($role!=1) {
            return $this->renderAPIError('Invalid user','');   
        }
        
        if(empty($post_id)) {
            return $this->renderAPIError("Invalid job",''); 
        }

        if(empty($applicant_id)) {
            return $this->renderAPIError("Invalid Applicant",''); 
        }

        $job = $this->mdl->callsql("SELECT COUNT(`id`) FROM job_post WHERE id=$post_id  ",'value');

        if(empty($job)) {
            return $this->renderAPIError("Invalid Job",''); 
        }

        $applicant = $this->mdl->callsql("SELECT COUNT(`id`) FROM applications WHERE user_id='".$applicant_id."' AND post_id='".$post_id."' ",'value');

        if(empty($applicant)) {
            return $this->renderAPIError("Invalid Job",''); 
        }
    
        $params = [];
        $params['post_id']              = $post_id;
        $params['applicant_id']         = $applicant_id;
        $params['user_id']              = $userId;
        $params['remark']               = $remark;
        
        if($this->mdl->insertReportApplicant($params))
        {
            return $this->renderAPI([], "Applicant Reported sucessfully", 'false', 'S22', 'true', 200); 
        }else{

            return $this->renderAPIError("Failed",'');    
        }
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 

}

}
