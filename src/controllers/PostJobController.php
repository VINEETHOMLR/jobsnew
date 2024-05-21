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




class PostJobController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->jobs = (new Jobs);
    }

    

    public function actionAddJob(){


        $input   = $_POST;
        //print_r($input);die();
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];

        $title          = issetGet($input,'title','');
        $latitude       = issetGet($input,'latitude','');
        $longitude      = issetGet($input,'longitude','');
        $location       = issetGet($input,'location','');
        $description    = issetGet($input,'description','');
        $category_id    = issetGet($input,'category_id','');
        
        $images         = issetGet($input,'images','');
        

        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }
        if( $latitude=="" || $longitude == "") {
            return $this->renderAPIError("Ivalid location",''); 
        }
        if(empty($location) || $location=="") {
            return $this->renderAPIError("Please select location",''); 
        }

        if(empty($description) || $description=="") {
            return $this->renderAPIError("Please Enter Description",''); 
        }

        $category = $this->jobs->callsql("SELECT `id` FROM category WHERE id=$category_id ",'value');

        if(empty($category))
            $this->renderAPIError("Invalid Category");

        if(empty($jobcheck))
            $this->renderAPIError("Invalid Job");
        
        $imagearray  = [];

        if(!empty($images)) {

            $counts = count($images);

            for($i=0;$i<$counts;$i++)
            {

                if(!$this->checkimage($images[$i])){

                    return $this->renderAPIError("Allowed image formats are jpeg,jpg,png",'');    
                    die(); 
                }
                $output_file = 'pro'.'_'.$i.rand().'_'.time().'_'.$userId;
                $profile_pic = $this->base64_to_jpeg($images[$i], $output_file);

                $imagearray[$i] = $profile_pic;

            }
            
            

        }

       

        $params = [];
        $params['title']            = $title;
        $params['latitude']         = $latitude;
        $params['longitude']        = $longitude;
        $params['location']         = $location;
        $params['description']      = $description;
        $params['category_id']      = $category_id;

        $params['images']           = json_encode($imagearray);
        $params['user_id']          = $userId;

        if($this->jobs->insertRecord($params))
        {
            return $this->renderAPI([], Raise::t('common','success_report'), 'false', 'S01', 'true', 200); 
        }else{

            return $this->renderAPIError(Raise::t('common','upload_err'),'');    
        }
        
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 

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

    $upload_path='web/upload/images/'; 
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
