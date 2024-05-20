<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class ClickCount extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "click_count";

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




    public function checkAlreadyClicked($book_id,$userId){


        $details = $this->callSql("SELECT * FROM $this->tableName WHERE  user_id=$userId AND book_id=$book_id ORDER BY id DESC ","rows");
        return $details;
    }


    public function updateCount($book_id,$user_id){

        $created_at = time();
        $query = "INSERT INTO $this->tableName (`user_id`,`book_id`,`created_at`) VALUES (:user_id,:book_id,:created_at)";

        $this->query($query);
        $this->bind(':user_id', $user_id);
        $this->bind(':book_id', $book_id);
        $this->bind(':created_at', $created_at);
        
        $this->execute();

     


        $totalCount = $this->callSql("SELECT count(id) FROM $this->tableName WHERE  book_id=$book_id ORDER BY id DESC ","value");

        

        $this->query("UPDATE book SET count = '$totalCount' where id = '$book_id'");
        $this->execute(); 

        
    }
    
    

    

    
   
    
}
