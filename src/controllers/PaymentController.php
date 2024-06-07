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
use src\models\Order;




class PaymentController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->jobs = (new Jobs);
        $this->Order = (new Order);
    }

   

public function actionCreateOrderId()
{

    $input   = $_POST;
        //print_r($input);die();
    $userObj = Raise::$userObj;
    $userId  = $userObj['id'];

    $post_id         = issetGet($input,'post_id','0');

    if($userObj['role_id']!='1') {
        return $this->renderAPIError("Please login as customer",''); 
    }
    if(empty($post_id)) {
        return $this->renderAPIError("Invalid job",''); 
    }

    $details = $this->jobs->findByPK($post_id);

    if(empty($details)) {

        return $this->renderAPIError("Invalid job",'');

    }

    $jobseeker_id = $details->jobseeker_id;

    if(empty($jobseeker_id)){
        return $this->renderAPIError("Invalid job",'');
    }

    $amount = $details->total_amount;

    if(empty($amount)){
        return $this->renderAPIError("Invalid job",'');
    }


    $params = [];
    $params['post_id']      = $post_id;
    $params['jobseeker_id'] = $jobseeker_id;
    $params['user_id']      = $userId;
    $params['amount']       = $amount;
    
    $order_id = $this->Order->addCreateOrder($params);
    if($order_id!=""){

        $message =  'Successfully Order Created';

    return $this->renderAPI(["orderId"=>$order_id], $message, 'false', 'S23', 'true', 200); 
    }
    return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 

}

function isValidNumber($input) {
    // This regular expression matches integers and decimal numbers
        $pattern = '/^\d+(\.\d+)?$/';
    
    // Check if the input matches the pattern
        return preg_match($pattern, $input);
}



}
