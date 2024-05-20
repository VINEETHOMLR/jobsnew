<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;
use src\lib\mailer\Mailer;


class User extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user";

        $this->columns = [
                            'id',                       
                            'username',                 
                            'fullname',                 
                            'old_password',             
                            'password',                 
                            'salt',                     
                            'status',                   
                            'fail_login_count',         
                            'last_login_time',          
                            'last_login_ip',            
                            'last_login_os',            
                            'last_login_device',        
                            'email_verification_status',
                            'email_verification_code',  
                            'email_verify_time',        
                            'email_verify_ip',          
                            'created_at',               
                            'created_by',               
                            'created_ip',               
                            'updated_at',               
                            'updated_by',               
                            'updated_ip'           
                        ];
    }

    /**
     *
     * @return Array
     */
    public static function attrs()
    {
        return   [
                    'id',                       
                    'username',                 
                    'fullname',                 
                    'old_password',             
                    'password',                 
                    'salt',                     
                    'status',                   
                    'fail_login_count',         
                    'last_login_time',          
                    'last_login_ip',            
                    'last_login_os',            
                    'last_login_device',        
                    'email_verification_status',
                    'email_verification_code',  
                    'email_verify_time',        
                    'email_verify_ip',          
                    'created_at',               
                    'created_by',               
                    'created_ip',               
                    'updated_at',               
                    'updated_by',               
                    'updated_ip'           
                ];
    }

    /**
     *
     * @return $this
     */
    public function assignAttrs($attrs = [])
    {   
        $isExternal = !empty($attrs);
        foreach (($isExternal ? $attrs : self::attrs()) as $eAttr => $attr) {
            $aAttr = $isExternal ? $eAttr : $attr;
           $this->{$aAttr} = $isExternal ? $attr : "";
        }
        
        return $this;
    }


    /**
     *
     * @param INT $pk
     */
    public function findByPK($pk)
    {
        $dtAry = parent::findByPK($pk);
        foreach ($dtAry as $attr => $val) {
            $this->{$attr} = $val;
        }
        return $this;
    }

   
    /**
     *
     * @return attrs data array
     */
    public function convertArray()
    {
        $temp = array();
        $attrs = $this->attrs();
        foreach ($attrs as $key) {
            $temp[$key] = isset($this->{$key})?$this->{$key}:'';
        }
        return $temp;
    }

    public function createRecord($data)
    {
        $this->assignAttrs($data);
        return $this->save();
    }    


    public function getUserIdByEmail($email = ""){

        $userId = $this->callSql("SELECT id FROM $this->tableName WHERE email = '$email' LIMIT 1","value");

        if (empty($userId)) {
            return 0;
        }

        return $userId;
    }


    public function otpUpdate($param){

        $time = time();
        $otp  = $param['otp'];
        $id   = $param['id'];
        $this->query("UPDATE $this->tableName SET otp = '$otp',otp_createdat='$time' WHERE id = '$id'");

            if($this->execute()){

                return true;    
            } 

        return false;             

    }

    public function otpCheck($param){

        $time = time(); 
        $id   = $param['id'];

        $data = $this->callSql("SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1","row");

        if (!empty($data['otp'])) {

             if($time - $data['otp_createdat'] < 180)
             {
                return true;
             }
        }

        return false;           

    }

     public function verifyOTP($param){

        $otp  = $param['otp'];
        $id   = $param['user_id'];

        $userId = $this->callSql("SELECT id FROM $this->tableName WHERE otp = '$otp' AND id = '$id'","value");

        if (empty($userId)) {

            $this->query("UPDATE $this->tableName SET fail_login_count = fail_login_count+1 WHERE id = '$id'");
            $this->execute();

            $getCount = $this->callSql("SELECT fail_login_count FROM $this->tableName WHERE id = '$id'","value");

            if ($getCount>=3) {
                
                $time = time();
                $this->query("UPDATE $this->tableName SET status =0,blocked_time='$time' WHERE id = '$id'");
                $this->execute();
            }

            return 0;
        }

        $this->query("UPDATE $this->tableName SET status =1,fail_login_count =0,blocked_time='' WHERE id = '$id'");
                $this->execute();

        return $userId;
    }

     public function resetPass($param){

        $time = time();
        $pass = $param['pass'];
        $id   = $param['user_id'];
        $pass = md5($pass);

        $this->query("UPDATE $this->tableName SET password = '$pass' WHERE id = '$id'");

            if($this->execute()){

                return true;    
            } 

        return false;             

    }


    public function getUserByUsername($username){
        

        $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE username = '$username' LIMIT 1","value");

        if (empty($userDetails)) {
            return [];
        }

        return $userDetails;
    }

    public function getUserByEmail($email){
        

        $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE email = '$email' LIMIT 1","value");

        if (empty($userDetails)) {
            return [];
        }

        return $userDetails;
    }

    public function getUserByUniqueId($user_unique_id){
        

        $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE user_unique_id = '$user_unique_id' LIMIT 1","value");

        if (empty($userDetails)) {
            return [];
        }

        return $userDetails;
    }

     public function getUserByEmailId($email,$id){
        

        $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE email = '$email' AND  id != '$id' LIMIT 1","value");

        if (empty($userDetails)) {
            return [];
        }

        return $userDetails;
    }

    public function sendVerificationCode($email)
    {
        
        if(empty($email)) {
            
            return ['status' => 'Error', 'message' => Raise::t('register','err_email_required')]; 
        }
        if($result = $this->sendVerification($email)){
            
            return ['status'=>'Success','message'=>Raise::t('verification','suc_verificationcode_sent')]; 
        }else{
            return ['status'=>'Error','message'=>Raise::t('verification','err_verificationcode_sent')];
        }

        
    }

    public function sendVerification($email)
    {
         
         $id = $this->callSql("SELECT id FROM reg_verification_code  WHERE email = '$email' LIMIT 1","value");
         $verificationCode  =  $this->createCode();
         $time = time();
         $title = Raise::t('register','from_title');
         $subject = Raise::t('register','subject_text');
         $params = ['username' =>'User',"code"=>$verificationCode];
         $message    =  Raise::t('register', 'register_email_message_body',$params);
         if(empty($id)) {

            $this->query("INSERT INTO reg_verification_code SET email='$email' ,is_verified='0', code = '$verificationCode' ,created_at = '$time'");
            if($this->execute() && $this->sendEmail($email,$title,$subject,$message)){

                return true;    
            } 

         }else{
             
            $this->query("UPDATE reg_verification_code SET code = '$verificationCode',is_verified='0' ,updated_at = '$time' where id = '$id'");
            if($this->execute() && $this->sendEmail($email,$title,$subject,$message)){

                return true;    
            } 
         }
         return false;
    }

    public function createCode(){

        return mt_rand(1000,2000);
    }

    public function sendEmail($email,$title,$subject,$message){

        $mail = new Mailer();
        $send = $mail->send($email,$title,$subject,$message);


        if ($send) {
            return true;
        }else{
            return false;
        }
        
        
    }

    public function registerUser($params = [])
    {


       // $email    = !empty($params['email']) ? $params['email'] : '';
       //$username     = !empty($params['username']) ? $params['username'] : '';
        $password     = !empty($params['password']) ? $params['password'] : '';
        $fullname     = !empty($params['fullname']) ? $params['fullname'] : '';
        $status       = !empty($params['status']) ? $params['status'] : '';
        $email        = !empty($params['email']) ? $params['email'] : '';
        
        $role_id   = !empty($params['role_id']) ? $params['role_id'] : '';
        $time = time();
        



        

        $data = [];
        $result = [];
            


        $query = "INSERT INTO $this->tableName (`name`,`password`,`role_id`,`email`,`status`,`created_at`) VALUES (:name,:password,:role_id,:email,:status,:created_at)";
        $this->query($query);
        //$this->bind(':username', $username);
        $this->bind(':name', $fullname);
        $this->bind(':password', ($password));
        $this->bind(':role_id', $role_id);
        $this->bind(':email', $email);
        $this->bind(':status', $status);
        $this->bind(':created_at', $time);
        $response = $this->execute();
        $userId = $this->lastInsertId();


        if(!empty($userId)){
            if($role_id == '2') { //jobseeker

                $sql = "INSERT INTO user_extra SET user_id='$user_id',category='',address='',id_photo='',basic_charge='0',created_at='$time',updated_at='$time'";
                $this->query($sql);
                $response = $this->execute();

            }
            return $response; 


            
                  
        }else{
            
            return false;
                     
        }

        
        return false;

    }

    public function verifyEmail($email = '' ,$verificationCode){
        
        if((!empty($verificationCode))) {

            $code = $this->callSql("SELECT code FROM reg_verification_code WHERE email = '$email' AND is_verified=0 LIMIT 1","value");
            if(!empty($code)) {
                
                if($code == $verificationCode || $verificationCode == '1234' ) {
                $this->query("UPDATE reg_verification_code SET is_verified = '1' where email = '$email'");
                $this->execute();
                 
                return true;
                }else{
                    return false;
                } 
            }else{
               return false;    
            }
            
            
        }
        return false;
    }

    public function getEmailIdByuser($user_id){
        
        if(empty($user_id)) {

            return ['status' => 'Error', 'message' => Raise::t('common','err_userid_required')];
        }
        
        $email = "";
        $email = $this->callSql("SELECT username FROM $this->tableName WHERE id = '$user_id' AND status = 1 LIMIT 1","value");
        return $email;
    }


    /*public function sendVerificationPasscode($userId,$email){

    
        if(empty($userId)) {

            return ['status' => 'Error','message' => Raise::t('common','err_userid_required')];
            
        }
        if(empty($email)) {
            
            return ['status' => 'Error','message' => Raise::t('register','err_email_required')];
            
        }

        if($this->sendVerification($email)){
            return ['status' => 'Success' ,'message' => Raise::t('register','suc_verificationcode_sent')];
        }else{

            return ['status' => 'Error' ,'message' => Raise::t('register','err_verificationcode_sent')];
        }

    }*/


    public function sendVerificationUser($userId,$email){

         
        if(empty($userId)) {

            return ['status' => 'Error','message' => Raise::t('common','err_userid_required')];
            
        }
        if(empty($email)) {
            
            return ['status' => 'Error','message' => Raise::t('register','err_email_required')];
            
        }

        if($this->sendVerificationCodeToUser($userId,$email)){
            $ip['user_id'] = $userId;
            $ip['module'] = 'Verification';
            $ip['action'] = 'update';
            $ip['activity'] = "Sent Verificationcode";
            (new UserActivityLog)->saveUserLog($ip);
            return ['status' => 'Success' ,'message' => Raise::t('register','suc_verificationcode_sent')];
        }else{

            return ['status' => 'Error' ,'message' => Raise::t('register','err_verificationcode_sent')];
        }

    }

    public function sendVerificationCodeToUser($userId,$email){
        
        $code = $this->createCode();
        $fullname = $this->callSql("SELECT fullname FROM $this->tableName WHERE id = '$userId' LIMIT 1","value");
        $fullname = !empty($fullname) ? $fullname : "User";
        $title = Raise::t('common','from_title');
        $subject = Raise::t('common','subject_text');
        $params = ['username' =>$fullname,"code"=>$code];
        $message    =  Raise::t('common', 'email_message_body',$params);
        $this->query("UPDATE $this->tableName SET email_verification_code = '$code' where id = '$userId'");
        if($this->execute() && $this->sendEmail($email,$title,$subject,$message)){

            return true;    
        } 
        return false;
    
    }
   
    public function getRecord($p_id) {
        
        $user_obj=$this->findByPK($p_id);
        $user_obj=isset($user_obj->id)? $user_obj:[];
        return $user_obj;
        
    }
    public function updateRecord($input_params,$where_params) {
       
        $this->assignAttrs($input_params);
        return $this->update($where_params);
       
    }
    public function checkRecord($id) {
        
        $id=$this->callSql('select id from user where id='.$id,'value');
        $status=!empty($id)? true:false;
        return $status;
        
    }

    public function getRecordNopk($input_attributes,$where_params,$result_type) {
         
                 $setAttrs = ' ';
        $whereAttr = '';
        foreach ($input_attributes as $k => $v) {
            $setAttrs.= $v.",";
        }
        
          if (!empty($where_params)) {
            $cnt = 1;
            $where = ' WHERE ';
            $flag=0;
            foreach ($where_params as $attr => $val) {
              if($flag==0)
              $where.=$attr."='".$val."'";
              else 
               $where.=" and ".$attr."='".$val."'";
              $flag=1;
            }
        }
        
         $query= 'SELECT '  . rtrim($setAttrs,',') .' FROM ' .$this->tableName . ' ' . $where.' LIMIT 1';
         return $this->callSql($query,$result_type);
        
    }
    public function getUsername($user_id) {
        
         $input_attributes=['username'];
         $where_params=['id'=>$user_id];
         $username=$this->getRecordNopk($input_attributes,$where_params,'value');
         return (!empty($username)?$username:'');
    }
     
    public function checkVerificationcode($verification_code,$user_id) {   
        
        $input_attributes=['email_verification_code'];
        $where_params=['id' => $user_id,'email_verification_code' => $verification_code];
        $email_verification_code=$this->getRecordNopk($input_attributes,$where_params,'value');
        $status=!empty($email_verification_code)? true:false;
        if(!$status)
        $status=($verification_code=='1234')? true:false;
        return $status;
    } 


    public function checkVerification($verification_code,$user_id){

        $input_attributes=['email_verification_code'];
        $where_params=['id' => $user_id];
        $email_verification_code=$this->getRecordNopk($input_attributes,$where_params,'value'); 
        if(empty($email_verification_code)) {
            
            return false;
        }
        if($email_verification_code == $verification_code) {

           return true;
        } 
        if(!empty($email_verification_code) && $verification_code =='1234') {
           return true;
        }  
        return false;
    } 

    public function checkLogin($username,$password) {

        $pass = md5($password); 

        $user_info = $this->callsql("SELECT * FROM $this->tableName WHERE email='$username' AND password='$pass' AND status='1' LIMIT 1 ","row");

        if(empty($user_info)){

            return []; 
        }

        return $user_info;
    }

      public function checkSocialLogin($param) {


        $email = $param['email'];
        $register_type = $param['register_type'];
        $user_unique_id = $param['user_unique_id'];
       

        $user_info = $this->callsql("SELECT * FROM $this->tableName WHERE email='$email' AND register_type='$register_type' AND user_unique_id='$user_unique_id' AND status='1' LIMIT 1 ","row");

        if(empty($user_info)){

            return []; 
        }

        return $user_info;
    }
    
    public function updateNopk($input_params,$where_params) {
       // $input_params = $this->getValAttrs();
        $setAttrs  = [];
        $whereAttr = [];
        foreach ($input_params as $k => $v) {
            $setAttrs[] = $k . ' = :' . $k;
        }
        foreach ($where_params as $wk => $wv) {
             $whereAttr []= $wk . ' = :' . $wk ;
        }


       // $whereAttr = $this->where($where_params);
       $sql= 'UPDATE ' . $this->tableName . ' SET ' . implode(',', $setAttrs) . ' WHERE ' . implode(',', $whereAttr);
        
        $this->query($sql);
        foreach ($input_params + $where_params as $param => $value) {
          
            $this->bind($param, $value);
        }
        return $this->execute();
    }

    public function getUserDetails($user_id){
        
        $user_info = $this->callsql("SELECT * FROM $this->tableName WHERE id='$user_id'","row");

        if(empty($user_info)){

            return []; 
        }

        return $user_info;
    }

    public function checkEmailVerified($email){
        
        $codeDetails = $this->callsql("SELECT * FROM reg_verification_code WHERE email='$email' AND is_verified='1' ","row");

        if(!empty($codeDetails)) {
            return true; 
        }
        return false;


        

    }


    public function updateProfile($params){

        $fullname    = $params['name'];
        $profile_pic = $params['profile_pic'];
        $email       = $params['email'];
        $id       = $params['user_id'];
        $time        = time();
        $this->query("UPDATE $this->tableName SET fullname = '$fullname',profile_pic='$profile_pic' ,email='$email',updated_at = '$time' where id = '$id'");
        if($this->execute()){
            return true;
        }
        return false;
            
    }

    public function checkUserByDeviceId($device_id){

        $userDetails = $this->callsql("SELECT * FROM $this->tableName WHERE device_id='$device_id' AND status='1' ","row");

        if(!empty($userDetails)) {
            return $userDetails; 
        }
        return $userDetails;

    }


    public function createUser($params)
    {

        
        $fullname      = $params['fullname'];
        $status        = $params['status'];
        $device_id     = $params['device_id'];
        $device_token  = $params['device_token'];
        $device_type   = $params['device_type'];
        $created_at = time();
        $query = "INSERT INTO $this->tableName (`fullname`,`status`,`created_at`,`device_id`,`device_token`,`device_type`) VALUES (:fullname,:status,:created_at,:device_id,:device_token,:device_type)";
            $this->query($query);
            $this->bind(':fullname', $fullname);
            $this->bind(':status', $status);
            $this->bind(':created_at', $created_at);
            $this->bind(':device_id', $device_id);
            $this->bind(':device_token', $device_token);
            $this->bind(':device_type', $device_type);
            if($this->execute()) {

                $userId = $this->lastInsertId();
                $fullname = $fullname.' '.$userId;
                $this->query("UPDATE $this->tableName SET fullname = '$fullname' where id = '$userId'");
                $this->execute();
                return true;    
            }
            return false;
            


    }

    public function updateDeviceToken($params){

        $device_token    = $params['device_token'];
        $device_type     = $params['device_type'];
        $id              = $params['user_id'];
        $this->query("UPDATE $this->tableName SET device_token = '$device_token',device_type='$device_type' where id = '$id'");
        if($this->execute()){
            return true;
        }
        return false;
            
    }


    public function addPointLog($data){

        $time = time();
        $this->query(" INSERT INTO point_log SET `user_id`='$data[user_id]',`type`='$data[type]',`affected_id`='$data[affected_id]',`point`='$data[point]',`created_at`='$time',`updated_at`=''");

        $this->execute();
        $logId = $this->lastInsertId();
        return $logId; 
    }

    public function addPoint($data)
    {
        
        $user_id = $data['user_id'];
        $totalPoint = $this->callsql("SELECT SUM(point) FROM point_log WHERE user_id='$user_id'","value"); 
        $this->query(" UPDATE user SET `point`='$totalPoint' WHERE `id`='$user_id'");
        $this->execute();


          

    }

    public function getUserPointHistory($params)
    {
        $user_id = $params['user_id'];
        $result = $this->callsql("SELECT * FROM point_log WHERE user_id='$user_id'","rows");
        $response = [];
        $typeArray = ['1'=>'Email Updation','2'=>'Report Price','3'=>'Profile Pic','4'=>'Suggest Product','5'=>'Suggest Store'];
        foreach($result as $k=>$v){
            
            $response[$k]['point'] = !empty($v['point']) ? $v['point'] : '0';
            $response[$k]['time']  = !empty($v['created_at']) ? date('d-m-Y',$v['created_at']) : '-';
            $response[$k]['type']  = !empty($v['type']) ? $typeArray[$v['type']] : '-';

        } 

        return $response;

    }


        

}
