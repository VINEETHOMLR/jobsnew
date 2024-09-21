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
use src\models\Category;



class ProfileController extends Controller
{
    
    protected $needAuth = false;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();

        $this->categorymdl = (new Category);
        $this->usermdl = (new User);
    }

    public function actionCompleteEmployeeProfile()
    {

        $input      = $_POST;
        $category      = issetGet($input,'category','');
        $experience   = issetGet($input,'experience','');
        $radius  = issetGet($input,'radius','');
        $phone  = issetGet($input,'phone','');
        $address  = issetGet($input,'address','');
        $idproof  = issetGet($input,'idproof','');
        $agree  = issetGet($input,'agree','');
        $role_id    = issetGet($input,'role_id','');//1- employer,2-jobseeker
        $userObj = Raise::$userObj;
        $userId = $userObj['id'];

        if(empty($userId)) {

            return $this->renderAPIError('Invalid Token',''); 

        }

        $request = json_encode($input);


        $sql = "INSERT INTO api_log_new SET request='$request',user_id='$userId'";
$this->usermdl->query($sql);
$this->usermdl->execute();

        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }

        if(empty($category)) {

            return $this->renderAPIError('Please select category to proceed',''); 

        }


        $localCatgeoryArray = [];

        foreach(explode(',',$category) as $key=>$value)
        {

           

            $details = $this->categorymdl->findByPK($value);
            if(empty($details) || $details->status!='1') {

                return $this->renderAPIError('Please select valid category to proceed',''); 
                die();

            }

            $parentCatgeoryDetails = $this->categorymdl->parentCategoryDetails($value);
            if($parentCatgeoryDetails['type'] == '1') {
                 
                 array_push($localCatgeoryArray, $value);
            }




            

        }

        if(empty($experience)) {

            return $this->renderAPIError('Please enter year of experience to proceed',''); 

        }

        if($experience && !$this->isValidNumber($experience)) {

            return $this->renderAPIError('Please enter a valid year of experience to proceed',''); 

        }

        if(empty($radius) && !empty($localCatgeoryArray)) {


            return $this->renderAPIError('Please enter radius to proceed',''); 

        }

        if($radius && !$this->isValidNumber($radius)) {

            return $this->renderAPIError('Please enter a valid radius to proceed',''); 

        }

        if(empty($phone)) {


            return $this->renderAPIError('Please enter phone to proceed',''); 

        }
        if(empty($address)) {


            return $this->renderAPIError('Please enter address to proceed',''); 

        }
        if(empty($idproof)) {


            return $this->renderAPIError('Please upload id proof  to proceed',''); 

        }

        if(!empty($idproof)) {

            if(!$this->checkimage($idproof)){

                return $this->renderAPIError("Allowed image formats are jpeg,jpg,png",'');    
                die(); 
            }

            $output_file = 'idproof'.'_'.rand().'_'.time().'_'.$userId;
            $idproofimage = $this->base64_to_jpeg($idproof, $output_file);
            
            
            

        }

        if(empty($agree) && $agree!=1) {


            return $this->renderAPIError('Please agree the terms and conditions to proceed',''); 

        }


        $params = [];  
        $params['category']   = $category;
        $params['experience'] = $experience;
        $params['radius']     = $radius;
        $params['phone']      = $phone;
        $params['address']    = $address;
        $params['idproof']    = $idproofimage;
        $params['user_id']    = $userId;
        if($this->usermdl->competeProfile($params)){

        return $this->renderAPI([], "Sucessfully updated the details", 'false', 'S14', 'true', 200); 

        }else{

            return $this->renderAPIError("Failed to update the details",'');    
        }
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 



    }

    public function actionDashboard()
    {

        $userObj = Raise::$userObj;
        $userId = $userObj['id'];
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }

        $data = $this->usermdl->getDashBoardData($userId);
        return $this->renderAPI($data, "Sucess", 'false', 'S14', 'true', 200); 



    }

    public function actionupdateBaseCharge()
    {

        $input      = $_POST;
        $base_charge      = issetGet($input,'base_charge','');
        $userObj = Raise::$userObj;
        $userId = $userObj['id'];
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }
        if(empty($base_charge)) {
            
            return $this->renderAPIError('Please enter base charge to proceed',''); 
        }
        if(!empty($base_charge) && !$this->isValidNumber($base_charge)) {
            
            return $this->renderAPIError('Please enter valid base charge to proceed',''); 
        }

        $params = [];
        $params['base_charge'] = $base_charge;
        $params['user_id']     = $userId;

        $basic_charge = 0;

        if($this->usermdl->updateBaseCharge($params)) {

            $basic_charge = $this->usermdl->getBaseCharge($params);
            $basic_charge = number_format($basic_charge,2);
            return $this->renderAPI(['basic_charge'=>$basic_charge], "Sucessfully updated the base charge", 'false', 'S14', 'true', 200);     
        }
        else{

            return $this->renderAPIError("Failed to update the details",'');    
        }
        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 




    }
    public function actionUpdateLocation()
    {

        $input          = $_POST;
        $latitude       = issetGet($input,'latitude','');
        $longitude      = issetGet($input,'longitude','');
        $userObj = Raise::$userObj;
        $userId = $userObj['id'];
        if($userObj['role_id']!='2') {
            
            return $this->renderAPIError('Please login as employee',''); 
        }
        if(empty($latitude)) {
            
            return $this->renderAPIError('Please pass latitude',''); 
        }
        if(empty($longitude)) {
            
            return $this->renderAPIError('Please pass longitude',''); 
        }

        $params = [];
        $params['latitude']  = $latitude;
        $params['longitude'] = $longitude;
        $params['user_id']   = $userId;

        if($this->usermdl->updateLocation($params))
        {

            return $this->renderAPI([], "Sucessfully updated", 'false', 'S14', 'true', 200);  

        }

        return $this->renderAPIError(Raise::t('common','something_wrong_text'),''); 
        

    }
    function isValidNumber($input) {
    // This regular expression matches integers and decimal numbers
        $pattern = '/^\d+(\.\d+)?$/';
    
    // Check if the input matches the pattern
        return preg_match($pattern, $input);
    }

    function base64_to_jpeg($base64_string, $output_file) {

        $upload_path='web/upload/idproof/'; 
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
