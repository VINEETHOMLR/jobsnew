<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class UserInfo extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_info";

        $this->columns = [
            'id', 
            'user_id', 
            'force_update_password',
            'kyc_status', 
            'security_pin',
            'is_deposit_allowed', 
            'is_withdrawal_allowed', 
            'is_swap_allowed', 
            'is_financial_allowed', 
            'kyc_id_number',
            'kyc_passport_number',
            'updated_at',
            'updated_by',
            'updated_ip',
            'created_at', 
            'created_ip',
            'created_by',  
        ];
    }

    /**
     *
     * @return Array
     */
    public static function attrs()
    {
        return  [
            'id', 
            'user_id', 
            'force_update_password',
            'kyc_status', 
            'security_pin',
            'is_deposit_allowed', 
            'is_withdrawal_allowed', 
            'is_swap_allowed', 
            'is_financial_allowed', 
            'kyc_id_number',
            'kyc_passport_number',
            'updated_at',
            'updated_by',
            'updated_ip',
            'created_at', 
            'created_ip',
            'created_by',
            
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

    public function verifyPasscode($params = [])
    {
     
        $user_id = !empty($params['user_id']) ? $params['user_id'] : "";
        $passcode = !empty($params['passcode']) ? $params['passcode'] : "";

        if(empty($user_id)){
            
            return ['status' => 'Error','message' => Raise::t('common','err_userid_required')];
        }

        if(empty($passcode)){
            
            return ['status' => 'Error','message' => Raise::t('verification','err_passcode_required')];
        }

        $currentPasscode = $this->callSql("SELECT security_pin FROM $this->tableName WHERE user_id = '$user_id' LIMIT 1","value");
        if($currentPasscode == $passcode) {
            
            return ['status' => 'Success','message' => Raise::t('verification','suc_passcode_verified')];  
        }else{
            
            return ['status' => 'Error','message' => Raise::t('verification','err_invalid_passcode')]; 
        } 

     
    }    
    public function updateRecord($input_params,$where_params) {

        $this->assignAttrs($input_params);
        return $this->update($where_params);
       
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


    public function getDetails($user_id){
         
         $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE user_id = '$user_id' LIMIT 1","row");

         return $userDetails; 
    }

    public function getDetailsByFinanceUsername($username)
    {

        $userDetails = $this->callSql("SELECT * FROM $this->tableName WHERE financial_username = '$username' LIMIT 1","row");

        return $userDetails; 

    }

    public function getAllUsersByUsername($username,$userId)
    {

        $users = $this->callSql("SELECT * FROM $this->tableName WHERE financial_username = '$username' AND user_id != $userId ","rows");


        
        if(!empty($users)) {
            
            return count($users);
        }
        return '0';
        

    }
}
