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

class ApplicantListController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = ['GetProducts'];

    public function __construct()
    {
        parent::__construct();
        $this->mdl = (new Applications);
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
}
