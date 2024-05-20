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
use src\models\Feedback;
use src\models\Store;




class ProductController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = ['GetProducts'];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->feedbackmdl = (new Feedback);
        $this->storemdl = (new Store);
    }

 

    public function actionFeedback(){


        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $store_id    = issetGet($input,'store_id','');
        $product_id   = issetGet($input,'product_id','');  
        $type   = issetGet($input,'type','');
        $base64_string   = issetGet($input,'image','');
       

        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }
        if(empty($store_id)) {
            return $this->renderAPIError(Raise::t('common','err_store_id'),''); 
        }
        if(empty($product_id)) {
            return $this->renderAPIError(Raise::t('common','err_product_id'),''); 
        }
        if(!is_numeric($store_id)) {
            return $this->renderAPIError(Raise::t('common','valid_store_id'),'');   
        }
        if(!is_numeric($product_id)) {
            return $this->renderAPIError(Raise::t('common','valid_product_id'),'');   
        }
         if(empty($type)) {
            return $this->renderAPIError(Raise::t('common','err_type'),''); 
        }

        if(!in_array($type,[1,2])) {
            return $this->renderAPIError(Raise::t('common','err_invalid_type'),''); 
        }

        /*if(($type==2)&&(empty($_FILES['image']))) {
            return $this->renderAPIError(Raise::t('common','err_image'),''); 
        }*/

        if(($type==2)&&(empty($base64_string))) {
            return $this->renderAPIError(Raise::t('common','err_image'),''); 
        }

        $params = [];
        $image  = '';
        if(($type==2)&&(!empty($base64_string))) {


            if(!$this->checkimage($base64_string)){

                return $this->renderAPIError("Allowed image formats are jpeg,jpg,png",'');    
                die(); 
            }

            $output_file = 'Fee'.'_'.rand().'_'.time().'_'.$userId;

            $image = $this->base64_to_jpeg($base64_string, $output_file);
            $params['image']           = $image;




        }

       /* if((!empty($_FILES['image'])&&($type==2))) {
            
            $path           = 'web/upload/feedback/';
            $file_name      = 'feedback_'.$userId.'_'.time();
            $uploadResponse = $this->uploadImage($_FILES['image'],$path,$file_name); 
            $response = $uploadResponse['status'];
            if($response == 'false') {
                
                return $this->renderAPIError($uploadResponse['message'],''); 
            }
            $image = $uploadResponse['filename']; 

            $params['image']           = $image;

        }*/

        $params['user_id']         = $userId;
        $params['store_id']        = $store_id;
        $params['product_id']      = $product_id;
        $params['type']            = $type;
        $params['is_accepted']     = 0;

        if($this->feedbackmdl->assignAttrs($params)->save()){
            
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

public function actionGetStore(){


        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $search_key   = issetGet($input,'search_key','');
        $product_id   = issetGet($input,'product_id','');  
        $latitude     = issetGet($input,'latitude','');  
        $longitude    = issetGet($input,'longitude','');  
        $open_status  = issetGet($input,'open_status','');   //1-opned,2-closed
        $distance     = issetGet($input,'distance','');   //1-near,2-far
        $radius       = issetGet($input,'radius','');   //1-near,2-far


        $language = Raise::$lang;    
        $input['language'] = empty($language)? 'en':$language;
       


        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }

        if(empty($latitude)) {
            return $this->renderAPIError(Raise::t('common','err_lat'),'');   
        }

        if(empty($longitude)) {
            return $this->renderAPIError(Raise::t('common','err_long'),'');   
        }

        $data = $this->storemdl->getStore($input);

        return $this->renderAPI($data, 'Store Data', 'false', 'S01', 'true', 200);
 

    }


    public function actionGetStoreProducts(){


        $input   = $_POST;
        $userObj = Raise::$userObj;
        $userId  = $userObj['id'];
        $search_key   = issetGet($input,'search_key','');
        $store_id   = issetGet($input,'store_id','');  


        $language = Raise::$lang;    
        $input['language'] = empty($language)? 'en':$language;

        if(empty($store_id)) {
            return $this->renderAPIError(Raise::t('common','err_store_id'),''); 
        }

        if(!is_numeric($store_id)) {
            return $this->renderAPIError(Raise::t('common','valid_store_id'),'');   
        }
       
        if(empty($userId)) {
            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }

        $data = $this->storemdl->getProductList($input);

        return $this->renderAPI($data, 'Store Data', 'false', 'S01', 'true', 200);
 

    }


 public function actionGetProducts(){


        $input          = $_POST;
        $userObj        = Raise::$userObj;
        $userId         = $userObj['id'];
        $lat            = issetGet($input,'lat','');
        $long           = issetGet($input,'long','');  
        $category       = issetGet($input,'category','');
        $sub_category   = issetGet($input,'sub_category','');  
        $product_name   = issetGet($input,'product_name','');
        
       

        $language = Raise::$lang;    
        $input['language'] = empty($language)? 'en':$language;

        if(empty($lat)) {
            return $this->renderAPIError(Raise::t('common','err_lat'),''); 
        }

        if(empty($long)) {
            return $this->renderAPIError(Raise::t('common','err_long'),''); 
        }

        

        if(empty($product_name) && empty($category) && empty($sub_category)) {
            return $this->renderAPIError(Raise::t('common','err_product_category_subcategory'),''); 
        }
        
        if(empty($product_name) && empty($category)) {

            return $this->renderAPIError(Raise::t('common','err_category'),''); 
        }


        if(empty($product_name) && empty($sub_category)) {
            return $this->renderAPIError(Raise::t('common','err_sub_Category'),''); 
        }


       
        if(empty($userId)) {

            return $this->renderAPIError(Raise::t('common','err_userid_required'),'');   
        }

        $input['product_name'] = empty($category) && empty($sub_category) ? $product_name : '';
        $input['category']     = empty($input['product_name']) ? $category : '';
        $input['sub_category'] = empty($input['product_name']) ? $sub_category : '';

      
        $data = $this->storemdl->getProduct($input);

        // print_r($data);exit;

        return $this->renderAPI($data, 'Store Data', 'false', 'S01', 'true', 200);
 

    }

public function actionSuggestProduct(){

    $input   = $_POST;
    $userObj = Raise::$userObj;
    $userId  = $userObj['id'];
    $product_name   = issetGet($input,'product_name','');

    if(empty($userId)) {

        return $this->renderAPIError(Raise::t('common','err_userid_required'),''); 

    }

    if(empty($product_name)) {

        return $this->renderAPIError(Raise::t('common','err_product_name'),''); 

    }

    $params = [];
    $params['user_id']  = $userId;
    $params['title_en'] = $product_name;
    $params['status']   = '1';

    if($this->storemdl->suggestProduct($params)){
        
        $data = [];
        return $this->renderAPI([], Raise::t('common','succ_product_suggest'), 'false', 'S01', 'true', 200); 

    }else{


        return $this->renderAPIError(Raise::t('common','err_product_suggest'),'');   
    }

    return $this->renderAPIError(Raise::t('common','something_wrong_text'),'');  



}

public function actionSuggestStore(){

    $input   = $_POST;
    $userObj = Raise::$userObj;
    $userId  = $userObj['id'];
    $store_name   = issetGet($input,'store_name','');
    $address   = issetGet($input,'address','');

    if(empty($userId)) {

        return $this->renderAPIError(Raise::t('common','err_userid_required'),''); 

    }

    if(empty($store_name)) {

        return $this->renderAPIError(Raise::t('common','err_store_name'),''); 

    }

    if(empty($address)) {

        return $this->renderAPIError(Raise::t('common','err_address'),''); 

    }

    $params = [];
    $params['user_id']    = $userId;
    $params['title_en']   = $store_name;
    $params['address_en'] = $address;
    $params['status']     = '1';

    if($this->storemdl->suggestStore($params)){
        
        $data = [];
        return $this->renderAPI([], Raise::t('common','succ_store_suggest'), 'false', 'S01', 'true', 200); 

    }else{


        return $this->renderAPIError(Raise::t('common','err_store_suggest'),'');   
    }

    return $this->renderAPIError(Raise::t('common','something_wrong_text'),'');  



}  

function base64_to_jpeg($base64_string, $output_file) {

    $upload_path='web/upload/feedback/'; 
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
