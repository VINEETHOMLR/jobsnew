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




class ProfileController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
    }

    public function actionIndex(){


        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        
        if(empty($userId)) {
             return $this->renderAPIError(Raise::t('common','err_userid_required'),''); 
        }

       
        $userDetails = [];
        $userDetails = $this->usermdl->getUserDetails($userId);

        $profile_pic = '';
        if($userDetails['register_type'] == '1' && !empty($userDetails['profile_pic'])) {
            
            $profile_pic = BASEURL.'web/upload/profile/'.$userDetails['profile_pic'];
        }

        if(in_array($userDetails['register_type'],['2','3','4']) && !empty($userDetails['profile_pic'])) {
            
            $profile_pic = $userDetails['profile_pic'];
        }


        if(empty($userDetails['profile_pic'])){
            
            $profile_pic = BASEURL.'web/upload/profile/default.jpeg';
        }

        $data = [];
        $data['profile_data']['name']  = !empty($userDetails['fullname']) ? $userDetails['fullname'] : '';
        $data['profile_data']['email'] = !empty($userDetails['email']) ? $userDetails['email'] : '';
        $data['profile_data']['point'] = !empty($userDetails['point']) ? $userDetails['point'] : '0';
        $data['profile_data']['profile_pic'] = $profile_pic;
                
        return $this->renderAPI($data, 'Profile Data', 'false', 'S01', 'true', 200);


        

    }

    public function actionUpdateProfile(){


        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $name    = issetGet($input,'name','');
        $email   = issetGet($input,'email','');
        $profile_pic   = issetGet($input,'profile_pic','');

        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }
        if(empty($name)) {
            return $this->renderAPIError(Raise::t('register','err_fullname_required'),''); 
        }
        if(empty($email)) {
            return $this->renderAPIError(Raise::t('register','err_email_required'),''); 
        }

        $emailExst = $this->usermdl->getUserByEmailId($email,$userId); 

        if(!empty($emailExst)) {
            return $this->renderAPIError(Raise::t('register','err_email_exists'),'');
        }
        
        $userDetails = $this->usermdl->getUserDetails($userId);
        

        if(!empty($profile_pic)) {
            
            if(!$this->checkimage($profile_pic)){

                return $this->renderAPIError("Allowed image formats are jpeg,jpg,png",'');    
                die(); 
            }
            $output_file = 'pro'.'_'.rand().'_'.time().'_'.$userId;
            $profile_pic = $this->base64_to_jpeg($base64_string, $output_file);

        }



        if(empty($profile_pic)) {

            $profile_pic = $userDetails['profile_pic'];
        }
        
        /*if(!empty($_FILES['profile_pic'])) {
            
            $path           = 'web/upload/profile/';
            $file_name      = 'profile_'.$userId.'_'.time();
            $uploadResponse = $this->uploadImage($_FILES['profile_pic'],$path,$file_name); 
            $response = $uploadResponse['status'];
            if($response == 'false') {
                
                return $this->renderAPIError($uploadResponse['message'],''); 
            }
            $profile_pic = $uploadResponse['filename']; 

        }*/

        $params = [];
        $params['name']        = $name;
        $params['profile_pic'] = $profile_pic;
        $params['email']       = $email;
        $params['user_id']     = $userId;

        if($this->usermdl->updateProfile($params)){


            $userDetails = [];
            $userDetails = $this->usermdl->getUserDetails($userId);

            $data = [];
            $data['name']  = !empty($userDetails['fullname']) ? $userDetails['fullname'] : '';
            $data['email'] = !empty($userDetails['email']) ? $userDetails['email'] : '';
            $data['point'] = !empty($userDetails['point']) ? $userDetails['point'] : '0';
            $data['profile_pic'] = !empty($userDetails['profile_pic']) ? BASEURL.'web/upload/profile/'.$userDetails['profile_pic'] : BASEURL.'web/upload/profile/default.jpeg';
            
            
            return $this->renderAPI($data, 'Profile Data', 'false', 'S01', 'true', 200);    
        }else{

            return $this->renderAPIError(Raise::t('common','upload_err'),'');    
        }
        
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 

    }

    public function actionChangepassword()
    {

        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        } 

        $password    = issetGet($input,'password','');
        $cpassword   = issetGet($input,'cpassword','');
        if(empty($password)){
            
            return $this->renderAPIError("Please enter password to proceed",''); 
        }
        if(empty($cpassword)){
            
            return $this->renderAPIError("Please enter confirm password to proceed",''); 
        }

        if($password != $cpassword) {
            
            return $this->renderAPIError("Password&confirm password should be same",''); 
        }

        $input_params = [];
        $input_params['password'] = md5($password);
        $where_params['id']       = $userId;
        if($this->usermdl->updateNopk($input_params,$where_params)){
            
            return $this->renderAPI([], 'Successfully changed the password', 'false', 'S01', 'true', 200); 
        }else{
            
            return $this->renderAPIError('Failed to update password','');
        }

        return $this->renderAPIError(Raise::t('common','something_wrong_text'),'');

    }

    public function actionLogout(){

        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        } 

        if((new User)->logutUser($userId))
        {
            return $this->renderAPI([], Raise::t('common','succ_logout'), 'false', 'S01', 'true', 200);    
        }else{


            return $this->renderAPIError(Raise::t('common','err_logout'),'');   
        }

        return $this->renderAPIError(Raise::t('common','something_wrong_text'),'');  


    }

    public function actionGetPointHistory(){

        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];

        if(empty($userId)) {
             return $this->renderAPIError(Raise::t('common','err_userid_required'),''); 
        }

        $params = [];
        $params['user_id'] = $userId;

        $list = $this->usermdl->getUserPointHistory($params);
        $data['point_history'] = $list;

        return $this->renderAPI($list, 'Point History', 'false', 'S01', 'true', 200);

    }



    function uploadImage($file,$path,$file_name){
  

   
        $file_tmp =$file['tmp_name'];
        $file_type=$file['type'];
        $file_ext=explode('/',$file_type);
        $file_ext = strtolower($file_ext[1]);
        $extensions= array("jpeg","jpg","png");
        $status = 'false';
        $message = "Something went wrong";
        $response = [];
        if(!in_array($file_ext,$extensions)) {
            
            $status  = 'false';
            $message = 'Only allowed jpg,jpeg,png images';
            return $response = ['status'=>$status,'message'=>$message];
        }
        
        if(move_uploaded_file($file_tmp,$path.$file_name.'.'.$file_ext))
        {
            $status = 'true';
            $message = '';
            return $response = ['status'=>$status,'message'=>$message,'filename'=>$file_name.'.'.$file_ext];
        }

        return $response = ['status'=>$status,'message'=>$message];



}


function base64_to_jpeg($base64_string, $output_file) {

    $upload_path='web/upload/profile/'; 
    $allowed = ['jpeg','jpg','png'];
    
    $imageInfo = explode(";base64,", $base64_string);
    $imgExt = str_replace('data:image/', '', $imageInfo[0]);      
    $image = str_replace(' ', '+', $imageInfo[1]);
    $imageName = $upload_path.$output_file.".".$imgExt;
    $ifp = fopen( $imageName, 'wb' ); 

    fwrite( $ifp, base64_decode( $image ) );
    fclose( $ifp );
    return $output_file.".".$imgExt;

}
function checkimage($base64_string){

    $allowed = ['jpeg','jpg','png'];
    $imageInfo = explode(";base64,", $base64_string);
    $imgExt = str_replace('data:image/', '', $imageInfo[0]); 
    if(in_array($imgExt, $allowed)){
        
       return true;
    }
    return false;
    

}
 


}
