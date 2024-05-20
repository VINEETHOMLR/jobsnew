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


class Feedback extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "feedback";

        $this->columns = [
                            'id',                       
                            'user_id',                 
                            'image',                 
                            'product_id',             
                            'store_id',                 
                            'created_at',                     
                            'updated_at',                   
                            'type',         
                            'admin_remark',          
                            'is_accepted'  
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
                            'user_id',                 
                            'image',                 
                            'product_id',             
                            'store_id',                 
                            'created_at',                     
                            'updated_at',                   
                            'type',         
                            'admin_remark',          
                            'is_accepted'           
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


}
