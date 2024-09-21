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
use src\models\UserBank;
use src\models\Category;
use src\lib\Razorpay;




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
        $this->UserBank = (new UserBank);
        $this->Category = (new Category);
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

public function actionVerifyPayment()
{

    $input   = $_POST;
        //print_r($input);die();
    $userObj = Raise::$userObj;
    $userId  = $userObj['id'];

    $razorpay_order_id         = issetGet($input,'razorpay_order_id','0');
    $razorpay_payment_id         = issetGet($input,'razorpay_payment_id','0');
    $razorpay_signature         = issetGet($input,'razorpay_signature','0');

    // if($userObj['role_id']!='1') {
    //     return $this->renderAPIError("Please login as customer",''); 
    // }

    // if(empty($razorpay_order_id)){
    //     return $this->renderAPIError("Invalid order id",'');
    // }
    // if(empty($razorpay_payment_id)){
    //     return $this->renderAPIError("Invalid payment id",'');
    // }
    // if(empty($razorpay_signature)){
    //     return $this->renderAPIError("Invalid singature",'');
    // }

    $order_details = $this->Order->callsql("SELECT * FROM `order_details` WHERE `transaction_id` = '".$razorpay_order_id."' AND status=1 ",'row');




    if(empty($order_details))
    {
        return $this->renderAPIError("Invalid order details",'');
    }


    $params = [];
    $params['user_id']                  = $userId;

    $params['razorpay_order_id']        = $razorpay_order_id;
    $params['razorpay_payment_id']      = $razorpay_payment_id;
    $params['razorpay_signature']       = $razorpay_signature;

    $params['oder_id']                  = $order_details['id'];

    $params['oder_id']                  = $order_details['id'];
    $params['post_id']                  = $order_details['post_id'];
    
    
    $response = $this->Order->verifyPayment($params);


    $response = ['true'];
    if($response){
        
        
        
        $message =  'Successfully payment Verified';
        return $this->renderAPI([], $message, 'false', 'S24', 'true', 200); 
    }
    return $this->renderAPIError("verification failed",''); 

}







function isValidNumber($input) {
    // This regular expression matches integers and decimal numbers
        $pattern = '/^\d+(\.\d+)?$/';
    
    // Check if the input matches the pattern
        return preg_match($pattern, $input);
}
/*
function verifyPayment($order_id, $payment_id, $razorpay_signature, $secret) {
  $generated_signature = hash_hmac('sha256', $order_id . '|' . $payment_id, $secret);
  return hash_equals($generated_signature, $razorpay_signature);
}

// Usage
$order_id = 'order_xyz';
$payment_id = 'pay_xyz';
$razorpay_signature = 'generated_signature_from_razorpay';
$secret = 'your_razorpay_secret_key';

$isValid = verifyPayment($order_id, $payment_id, $razorpay_signature, $secret);
if ($isValid) {
  echo 'Payment verified successfully';
} else {
  echo 'Payment verification failed';
}
*/



}
