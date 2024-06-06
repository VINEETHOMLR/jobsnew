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
use src\models\UserBank;
use src\lib\Razorpay;






class BankController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->userbank = (new UserBank);
        $this->razorpay = (new Razorpay);
        
    }

    public function actionAddbank()
    {
        
        $input             = $_POST;
        $account_number    = issetGet($input,'account_number','');
        $ifsc              = issetGet($input,'ifsc','');
        $beneficiary_name  = issetGet($input,'beneficiary_name','');
        //$account_type      = issetGet($input,'account_type','');
        $userObj = Raise::$userObj;
        $user_id = $userObj['id'];

        
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }

        $banks = $this->userbankmdl->getBanks(['user_id'=>$user_id]);
        if(!empty($banks)) {

            return $this->renderAPIError("Already added bank",''); 

        }

        if(empty($account_number)) {

            return $this->renderAPIError("Please enter account number to proceed",''); 

        }

        if(empty($ifsc)) {

            return $this->renderAPIError("Please enter IFSC code  to proceed",''); 

        }
        if(empty($beneficiary_name)) {

            return $this->renderAPIError("Please enter account holder name to proceed",''); 

        }
        // if(empty($account_type)) {

        //     return $this->renderAPIError("Please enter account type to proceed",''); 

        // }

        // if(!empty($account_type) && !in_array($account_type, [])) {

        // }

        $details = $this->usermdl->getDetails($user_id);
        $connect_id = $details['connect_id'];
        if(empty($details['connect_id'])) { //create connect id in razorpay
            
            $params = [];
            $params['name']    = $details['name'];
            $params['email']   = $details['email'];
            $params['type']    = 'customer';
            $params['user_id'] = $details['id'];
            $response = $this->razorpay->createAccount($params);
            if($response['status'] && $response['connect_id']) {

                $connect_id = $response['connect_id'];

            }else{


                return $this->renderAPIError("Failed to create account.Please try again ",''); 

            }

        
        }

        
        $params = [];
        $params['account_number']   = $account_number;
        $params['ifsc']             = $ifsc;
        $params['beneficiary_name'] = $beneficiary_name;
        $params['contact_id']       = $connect_id;
        $bankCreateResponse = $this->razorpay->createBankAccount($params);
        if($bankCreateResponse['status']) {

            return $this->renderAPI([], 'Successfully added bank', 'false', 'S01', 'true', 200);

        }

        return $this->renderAPIError("Something went wrong",''); 

    }

    public function actionDeleteBank()
    {

        $input             = $_POST;
        $id    = issetGet($input,'id','');
        $userObj = Raise::$userObj;
        $user_id = $userObj['id'];

        
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }
        if(empty($id)) {
            
            return $this->renderAPIError('Please select a bank to proceed',''); 
        }

        if($this->userbankmdl->deleteBank($id)){

            return $this->renderAPI([], 'Successfully deleted bank', 'false', 'S01', 'true', 200);

        }

        return $this->renderAPIError("Something went wrong",''); 


    }

    public function actionUpdateBank()
    {
        
        $input             = $_POST;
        $account_number    = issetGet($input,'account_number','');
        $ifsc              = issetGet($input,'ifsc','');
        $beneficiary_name  = issetGet($input,'beneficiary_name','');
        $id  = issetGet($input,'id','');
        //$account_type      = issetGet($input,'account_type','');
        $userObj = Raise::$userObj;
        $user_id = $userObj['id'];

        
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }

        if(empty($id)) {

            return $this->renderAPIError("Please select a bank to proceed",''); 

        }

        

        if(empty($account_number)) {

            return $this->renderAPIError("Please enter account number to proceed",''); 

        }

        if(empty($ifsc)) {

            return $this->renderAPIError("Please enter IFSC code  to proceed",''); 

        }
        if(empty($beneficiary_name)) {

            return $this->renderAPIError("Please enter account holder name to proceed",''); 

        }
        // if(empty($account_type)) {

        //     return $this->renderAPIError("Please enter account type to proceed",''); 

        // }

        // if(!empty($account_type) && !in_array($account_type, [])) {

        // }

        $details = $this->usermdl->getDetails($user_id);
        $connect_id = $details['connect_id'];
        if(empty($details['connect_id'])) { //create connect id in razorpay
            
            $params = [];
            $params['name']    = $details['name'];
            $params['email']   = $details['email'];
            $params['type']    = 'customer';
            $params['user_id'] = $details['id'];
            $response = $this->razorpay->createAccount($params);
            if($response['status'] && $response['connect_id']) {

                $connect_id = $response['connect_id'];

            }else{


                return $this->renderAPIError("Failed to create account.Please try again ",''); 

            }

        
        }

        
        $params = [];
        $params['account_number']   = $account_number;
        $params['ifsc']             = $ifsc;
        $params['beneficiary_name'] = $beneficiary_name;
        $params['contact_id']       = $connect_id;
        $bankCreateResponse = $this->razorpay->createBankAccount($params);
        if($bankCreateResponse['status']) {

            return $this->renderAPI([], 'Successfully updated bank', 'false', 'S01', 'true', 200);

        }

        return $this->renderAPIError("Something went wrong",''); 

    }

    

    
    


}
