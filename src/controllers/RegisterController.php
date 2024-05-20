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

        $new_file = '';
        if($_FILES){
            if($_FILES['profile_pic']['size']!= 0 ){ 

                     $filename   = $_FILES['profile_pic']['name'];
                     $temp_name  = $_FILES['profile_pic']['tmp_name'];

                     $path_parts  = pathinfo($filename);
                     $extension   = $path_parts['extension'];
                     $image_array = array('JPG','png','JPEG','jpeg','jpg');
                     $new_file    = 'P'.time().rand().'.'.$extension;

                     $target_file = BASEPATH."web/upload/profile/".$new_file;
                     $FileType    = pathinfo($target_file,PATHINFO_EXTENSION);
                     $path        = pathinfo($target_file);
                     move_uploaded_file ($temp_name, $target_file);

                     
            }
        }


        $params  = [];
        $params['fullname']    = $fullname;
        $params['email']       = $email;
        $params['password']    = md5($password);
        $params['profile_pic'] = $new_file;
        $params['status']      = 1;
        //$params['email']    = $email;
        if($response = $this->usermdl->registerUser($params)){


            if($new_file) {

                $data = [];
                $data['user_id']     = $response;
                $data['type']        = '3';
                $data['affected_id'] = '';
                $data['point']       = '50';
                $this->usermdl->addPointLog($params);
                $this->usermdl->addPoint($params);

            }


            return $this->renderAPI([], Raise::t('register','suc_register'), 'false', 'S01', 'true', 200);

        }else{
            
            return $this->renderAPI([], Raise::t('register','err_failed_create_user_text'), 'false', '', 'true', 200);
        }
        return $this->renderAPI([], Raise::t('common','something_wrong_text'), 'false', '', 'true', 200);


    }


      public function actionSocialMediaReg(){



        $input            = $_POST;
        $fullname         = issetGet($input,'fullname','');
        $email            = issetGet($input,'email','');
        $register_type    = issetGet($input,'register_type','');
        $user_unique_id   = issetGet($input,'user_unique_id','');
        $profile_pic   = issetGet($input,'profile_pic','');

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
  
        if(empty($register_type)) {

            return $this->renderAPIError(Raise::t('register','err_reg_type_required_text'),''); 
        }

        if(!in_array($register_type,[2,3,4])) {
            return $this->renderAPIError(Raise::t('common','err_invalid_type'),''); 
        }

        if(empty($user_unique_id)) {
            return $this->renderAPIError(Raise::t('register','err_user_unique_id_required_text'),''); 

        }

        $details = $this->usermdl->getUserByUniqueId($user_unique_id);
        if(!empty($details)) {
            
            return $this->renderAPIError(Raise::t('register','err_uniq_id_exists'),'');
        }


        $params  = [];
        $params['fullname']        = $fullname;
        $params['email']           = $email;
        $params['register_type']   = $register_type;
        $params['user_unique_id']  = $user_unique_id;
        $params['profile_pic']     = $profile_pic;
        $params['status']          = 1;
        if($response = $this->usermdl->registerUser($params)){

            
            if($profile_pic) {

                $data = [];
                $data['user_id']     = $response;
                $data['type']        = '3';
                $data['affected_id'] = '';
                $data['point']       = '50';
                $this->usermdl->addPointLog($params);
                $this->usermdl->addPoint($params);

            }
             
            return $this->renderAPI([], Raise::t('register','suc_register'), 'false', 'S01', 'true', 200);

        }else{
            
            return $this->renderAPI([], Raise::t('register','err_failed_create_user_text'), 'false', '', 'true', 200);
        }
        return $this->renderAPI([], Raise::t('common','something_wrong_text'), 'false', '', 'true', 200);


    }



public function is_url($url){

       if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        } 
        return false;
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
