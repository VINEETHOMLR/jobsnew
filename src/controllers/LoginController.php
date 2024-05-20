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
use src\models\UserTokenList;
use src\models\UserActivityLog;



class LoginController extends Controller
{
     protected $needAuth = false;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
    }

    
    public function actionLogin()
    {
      
        echo "hai";exit;
       // $input      = Router::postAll();
        $input      = $_POST;
        $email  = issetGet($input,'email','');
        $password  = issetGet($input,'password','');

        if(empty($email)) {
            return $this->renderAPIError(Raise::t('register','err_email_required'),'');  
        }
        if(!$this->validateEmail($email)){

            return $this->renderAPIError(Raise::t('register','err_invalid_email_text'),'');    
        }

        if(empty($password)) {
            return $this->renderAPIError(Raise::t('register','err_password_required_text'),''); 
        }

        $userDetails = (new User)->checkLogin($email,$password);




        if (empty($userDetails)) {
            
            return $this->renderAPIError(Raise::t('login','err_invalid_credentials'),''); 

            
        } 




       // $user_info = (new UserInfo)->findByPK($user['id'])->convertArray();
       /* $driver_info = (new Driver_user)->getDetails($driver['id']);


        echo "<pre>";
        print_r($driver_info);exit;
*/


        $token = $this->generateToken($userDetails['id']);





        $userSystemInfo = Helper::getUserSystemInfo();




        $insert['user_id']               =  $userDetails['id'];             
        $insert['token']                 =  $token;                   
        $insert['device_id']             =  $userSystemInfo['device_id'];          
        $insert['device_model']          =  $userSystemInfo['device_model'];        
        $insert['device_os']             =  $userSystemInfo['device_os'];          
        $insert['device_imei']           =  $userSystemInfo['device_imei'];      
        $insert['device_manufacturer']   =  $userSystemInfo['device_manufacturer'];
        $insert['device_appversion']     =  $userSystemInfo['device_appversion'];
        $insert['language']              =    $userSystemInfo['language'];         
        $insert['medium']                =    "1";            
        $insert['created_at']            =  time();         
        $insert['created_ip']            =    $userSystemInfo['ip'];       
        $insert['status']                =  '1';            
        $insert['last_seen']             =  time();      

        (new UserTokenList)->assignAttrs($insert)->save();




        $updateUser['last_login_time']        =  time();  
        $updateUser['last_login_ip']          =  $userSystemInfo['ip'];        
        $updateUser['last_login_os']          =  $userSystemInfo['device_os'];        
        $updateUser['last_login_device']      =  $userSystemInfo['device_model'];        
        $updateUser['id']      =  $userDetails['id'];        

        (new User)->assignAttrs($updateUser)->update();




        $ip['module']   = 'Login';
        $ip['action']   = 'login';
        $ip['activity'] = "User login";
        $ip['user_id']  = $userDetails['id'];
        (new UserActivityLog)->saveUserLog($ip);

       
        $redisKey = 'ut-'.$token;

        /*$redis = (new RRedis);

        $redisKey = 'ut-'.$token;

        if ($redis->exists($redisKey)){
            $redis->del($redisKey);
        }

        $redis->set($redisKey,$player_arr,7200);*/

        $data = array(
                    "id"=> (string)$userDetails['id'],
                    "name"=> (string)$userDetails['fullname'],
                    "status"=> "1",
                    "last_login_time"=> (string)$userDetails['last_login_time'],
                    "last_login_ip"=> (string)$userDetails['last_login_ip'],
                    "token"=> (string)$redisKey);
        
        return $this->renderAPI($data, Raise::t('login','suc_login'), 'false', 'S01', 'true', 200);
    
    }


     public function actionSocialMediaLogin()
    {
      
        
       // $input      = Router::postAll();
        $input      = $_POST;
        $email            = issetGet($input,'email','');
        $register_type    = issetGet($input,'register_type','');
        $user_unique_id   = issetGet($input,'user_unique_id','');



        if(empty($email)) {
            return $this->renderAPIError(Raise::t('register','err_email_required'),'');  
        }
        if(!$this->validateEmail($email)){

            return $this->renderAPIError(Raise::t('register','err_invalid_email_text'),'');    
        }

        if(empty($register_type)) {

            return $this->renderAPIError(Raise::t('register','err_reg_type_required_text'),''); 
        }

        if(!in_array($register_type,[1,2,3,4])) {
            return $this->renderAPIError(Raise::t('common','err_invalid_type'),''); 
        }

        if(empty($user_unique_id)) {
            return $this->renderAPIError(Raise::t('register','err_user_unique_id_required_text'),''); 

        }


        $userDetails = (new User)->checkSocialLogin($input); 

        if (empty($userDetails)) {
            
            return $this->renderAPIError(Raise::t('login','err_invalid_credentials'),''); 

            
        } 


        $token = $this->generateToken($userDetails['id']);
        $userSystemInfo = Helper::getUserSystemInfo();

        $insert['user_id']               =  $userDetails['id'];             
        $insert['token']                 =  $token;                   
        $insert['device_id']             =  $userSystemInfo['device_id'];          
        $insert['device_model']          =  $userSystemInfo['device_model'];        
        $insert['device_os']             =  $userSystemInfo['device_os'];          
        $insert['device_imei']           =  $userSystemInfo['device_imei'];      
        $insert['device_manufacturer']   =  $userSystemInfo['device_manufacturer'];
        $insert['device_appversion']     =  $userSystemInfo['device_appversion'];
        $insert['language']              =    $userSystemInfo['language'];         
        $insert['medium']                =    "1";            
        $insert['created_at']            =  time();         
        $insert['created_ip']            =    $userSystemInfo['ip'];       
        $insert['status']                =  '1';            
        $insert['last_seen']             =  time();      

        (new UserTokenList)->assignAttrs($insert)->save();




        $updateUser['last_login_time']        =  time();  
        $updateUser['last_login_ip']          =  $userSystemInfo['ip'];        
        $updateUser['last_login_os']          =  $userSystemInfo['device_os'];        
        $updateUser['last_login_device']      =  $userSystemInfo['device_model'];        
        $updateUser['id']      =  $userDetails['id'];        

        (new User)->assignAttrs($updateUser)->update();

        $ip['module']   = 'Login';
        $ip['action']   = 'login';
        $ip['activity'] = "User login";
        $ip['user_id']  = $userDetails['id'];
        (new UserActivityLog)->saveUserLog($ip);

       
        $redisKey = 'ut-'.$token;

        $data = array(
                    "id"=> (string)$userDetails['id'],
                    "name"=> (string)$userDetails['fullname'],
                    "status"=> "1",
                    "last_login_time"=> (string)$userDetails['last_login_time'],
                    "last_login_ip"=> (string)$userDetails['last_login_ip'],
                    "token"=> (string)$redisKey);
        
        return $this->renderAPI($data, Raise::t('login','suc_login'), 'false', 'S01', 'true', 200);
    
    }


    public function generateToken($user_id)
    {

        (new UserTokenList)->expireUserToken($user_id);

        do {

            $token = (function_exists('mcrypt_create_iv')) ? bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)) : bin2hex(openssl_random_pseudo_bytes(32));

            $isTokenExist = (new UserTokenList)->isTokenExist($token);

        } while ($isTokenExist);

        return $token;
    }


function checkEmail($email) {
         return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? false : true;
    }
public function validateEmail($email){
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
    return false;
}


}
