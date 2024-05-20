<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class WalletLog extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "wallet_log";

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



    public function insertWalletLog($data){


       $query = "INSERT INTO $this->tableName (`user_id`,`credit_type`,`transaction_type`,`value`,`created_at`,`created_by`,`created_ip`,`before_bal`,`after_bal`) VALUES (:user_id,:credit_type,:transaction_type,:value,:created_at,:created_by,:created_ip,:before_bal,:after_bal)";

        $this->query($query);
        $this->bind(':user_id', $data['user_id']);
        $this->bind(':credit_type', $data['credit_type']);
        $this->bind(':transaction_type', $data['transaction_type']);
        $this->bind(':value', $data['value']);
        $this->bind(':created_at', $data['created_at']);
        $this->bind(':created_by', $data['created_by']);
        $this->bind(':created_ip', $data['created_ip']);
        $this->bind(':before_bal', $data['before_bal']);
        $this->bind(':after_bal', $data['after_bal']);
      

        return $this->execute();


    }  


    
    
    

    

    
   
    
}
