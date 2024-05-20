<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Jobs extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "job_post";

        $this->columns = [
            'id', 
            'title', 
            'latitude',
            'longitude',  
            'location',
            'description',
            'status',
            'category_id',
            'jobseeker_id',
            'created_at',
            'updated_at',
            'labour_cost',
            'material_cost',
            'total_amount',
            'payment_status ',
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
            'latitude',
            'longitude',  
            'location',
            'description',
            'status',
            'category_id',
            'jobseeker_id',
            'created_at',
            'updated_at',
            'labour_cost',
            'material_cost',
            'total_amount',
            'payment_status ',
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



    function insertRecord($params){

        $query = "INSERT INTO $this->tableName (`title`,`user_id`,`latitude`,`longitude`,`location`,`images`,`description`,`status`,`category_id`,`created_at`) VALUES (:title,:user_id,:latitude,:longitude,:location,:images,:description,:status,:category_id,:created_at)";
            $this->query($query);
            $this->bind(':title',       $params['title']);
            $this->bind(':user_id',     $params['user_id']);
            $this->bind(':latitude',    $params['latitude']);
            $this->bind(':longitude',   $params['longitude']);
            $this->bind(':location',    $params['location']);
            $this->bind(':images',      $params['images']);
            $this->bind(':description', $params['description']);
            $this->bind(':status',      1);
            $this->bind(':category_id', $params['category_id']);
            $this->bind(':created_at',  time());
           
            if($this->execute()){
                return true;
            }
            return false;
            
    }  



    
    

    

    
   
    
}
