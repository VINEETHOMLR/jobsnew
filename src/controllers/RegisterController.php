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
use src\lib\mailer\Mailer;




class RegisterController extends Controller
{
    
    protected $needAuth = false;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
    }

    public function actionIndex(){



        $input            = $_POST;
        $fullname         = issetGet($input,'fullname','');
        $email            = issetGet($input,'email','');
        $password         = issetGet($input,'password','');
        $confirmpassword  = issetGet($input,'confirmpassword','');
        $role             = issetGet($input,'role',''); //1- eployer,2- jobseeker

        if(empty($fullname)) {
            return $this->renderAPIError(Raise::t('register','err_fullname_required'),'');  
        }

        if(empty($email)) {

            return $this->renderAPIError(Raise::t('register','err_email_required'),'');   
        }
        if(!$this->validateEmail($email)){

            return $this->renderAPIError(Raise::t('register','err_invalid_email_text'),'');    
        }

        
        $userDetails = $this->usermdl->getUserByEmail($email);
        if(!empty($userDetails)) {
            
            return $this->renderAPIError(Raise::t('register','err_email_exists'),'');
        }

       
        if(empty($password)) {

            return $this->renderAPIError(Raise::t('register','err_password_required_text'),''); 
        }
        if(empty($confirmpassword)) {
            return $this->renderAPIError(Raise::t('register','err_confirmpassword_required_text'),''); 

        }
        if($password != $confirmpassword) {
            
            return $this->renderAPIError(Raise::t('register','err_same_password_confirm_password_text'),''); 

        }

        if(empty($role)) {

            return $this->renderAPIError("Please pass role to proceed",'');   
        }

        if(!in_array($role, ['1','2'])) {

            return $this->renderAPIError("Please pass valid role to proceed",'');   

        }



       


        $params  = [];
        $params['fullname']    = $fullname;
        $params['email']       = $email;
        $params['password']    = md5($password);
        $params['status']      = 1;
        $params['role_id']     = $role;
        if($response = $this->usermdl->registerUser($params)){
            
            return $this->renderAPI([], Raise::t('register','suc_register'), 'false', 'S01', 'true', 200);

        }else{
            
            return $this->renderAPI([], Raise::t('register','err_failed_create_user_text'), 'false', '', 'true', 200);
        }
        return $this->renderAPI([], Raise::t('common','something_wrong_text'), 'false', '', 'true', 200);


    }


     



public function validateEmail($email){
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
    return false;
}




  public function actionForgotPassword(){ 
     
        $input   = $_POST;
        $email   = issetGet($input,'email','');

        if(empty($email)) {
            return $this->renderAPIError(Raise::t('register','err_email_required'),'');   
        }

        if($id=$this->usermdl->getUserIdByEmail($email)){

            $otp = rand(1, 10000);

            $param['otp']    = $otp;
            $param['id']     = $id;
            $param['email']  = $email;

            if($this->usermdl->otpCheck($param)){ 
 
                return $this->renderAPI(['user_id'=>$id], Raise::t('common','use_old_otp'), 'false', 'S01', 'true', 200);
            }

            if($this->usermdl->otpUpdate($param)){ 

                // $this->sendEmail($param);

                return $this->renderAPI(['user_id'=>$id], Raise::t('common','sent_otp'), 'false', 'S01', 'true', 200);

            }

            return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 
        }

        return $this->renderAPIError(Raise::t('common','email_err'),''); 
  
    }


    public function sendEmail($params){

        $otp          = $params['otp'];
        $title        = 'OTP';
        $subject      = 'OTP';
        $message      = 'Your OTP '.$otp;
        $email        = $params['email'];

        $mail = new Mailer();
        $send = $mail->send($email,$title,$subject,$message);

    }

      public function actionOTPVerify(){ 
     
        $input   = $_POST;
        $otp   = issetGet($input,'otp','');
        $user_id   = issetGet($input,'user_id','');
        $data['status'] = false;

        if(empty($otp)) {
            return $this->renderAPIError(Raise::t('common','otp_err'),'');   
        }
        if(empty($user_id)) {
            return $this->renderAPIError(Raise::t('common','user_id_err'),'');   
        }

        if($this->usermdl->verifyOTP($input)){

                $data['status'] = true;
                $data['user_id'] = $user_id;
                return $this->renderAPI($data, 'Success', 'false', 'S01', 'true', 200);

        }

        return $this->renderAPI($data, Raise::t('common','otp_matching_err'), 'false', 'S01', 'true', 200);
  
    }

     public function actionresetPassword(){ 
     
        $input     = $_POST;
        $pass      = issetGet($input,'pass','');
        $con_pass  = issetGet($input,'con_pass','');
        $user_id   = issetGet($input,'user_id','');


        if(empty($pass)) {
            return $this->renderAPIError(Raise::t('register','err_password_required_text'),'');   
        }
        if(empty($con_pass)) {
            return $this->renderAPIError(Raise::t('register','err_confirmpassword_required_text'),'');   
        }
        if(empty($user_id)) {
            return $this->renderAPIError(Raise::t('common','user_id_err'),'');   
        }

        if($pass == $con_pass){

            if($this->usermdl->resetPass($input)){

                return $this->renderAPIError(Raise::t('common','forgot_success'),''); 

            }

        } else {

        return $this->renderAPIError(Raise::t('register','err_same_password_confirm_password_text'),''); 

       }

       return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 
  
    }


}
