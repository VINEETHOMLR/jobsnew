<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class UserLoginLog extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_token_list";

        $this->columns = [
                    'id',                    
                    'user_id',               
                    'token',                 
                    'expired_at',            
                    'device_id',             
                    'device_model',          
                    'device_os',             
                    'device_imei',           
                    'device_manufacturer',   
                    'device_appversion',     
                    'language',              
                    'medium',                
                    'created_at',            
                    'created_ip',            
                    'status',                
                    'last_seen',             
                    'logout_type',           
                    'logout_at',             
                    'logout_ip'         
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
                    'token',                 
                    'expired_at',            
                    'device_id',             
                    'device_model',          
                    'device_os',             
                    'device_imei',           
                    'device_manufacturer',   
                    'device_appversion',     
                    'language',              
                    'medium',                
                    'created_at',            
                    'created_ip',            
                    'status',                
                    'last_seen',             
                    'logout_type',           
                    'logout_at',             
                    'logout_ip'         
            
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

    public function expireUserToken($user_id) {


        $this->query("UPDATE $this->tableName SET status = '0',expired_at = '".time()."' WHERE user_id = '".$user_id."' AND status = 1 ");

        $this->execute();
    }

    public function isTokenExist($token) {

        $count =  $this->callSql("SELECT COUNT(id) FROM $this->tableName WHERE token = '".$token."' AND status = 1 ","value");

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

}
