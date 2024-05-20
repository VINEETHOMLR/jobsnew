<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class UserPurchase extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_purchase";

        $this->columns = [
            'id', 
            'title', 
            'message',
            'createtime',  
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
            'title', 
            'message', 
            'createtime', 
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


    public function checkUserPaid($user_id,$category_id){
        
        $paidDetails = $this->callSql("SELECT * FROM $this->tableName WHERE  category_id=$category_id AND user_id=$user_id AND  status=1  ","row");
        return $paidDetails;   

    } 

    function insertRecord($params){


        $query = "INSERT INTO $this->tableName (`user_id`,`name`,`email`,`category_id`,`amount`,`transaction_time`,`transaction_reference_id`,`status`,`created_at`) VALUES (:user_id,:name,:email,:category_id,:amount,:transaction_time,:transaction_reference_id,:status,:created_at)";
            $this->query($query);
            $this->bind(':user_id', $params['user_id']);
            $this->bind(':name', $params['name']);
            $this->bind(':email', $params['email']);
            $this->bind(':category_id', $params['category_id']);
            $this->bind(':amount', $params['amount']);
            $this->bind(':transaction_time', $params['transaction_time']);
            $this->bind(':transaction_reference_id', $params['transaction_reference_id']);
            $this->bind(':status', $params['status']);
            $this->bind(':created_at', $params['created_at']);
           

            if($this->execute()){
                return true;
            }
            return false;
            
    }  



    
    

    

    
   
    
}
