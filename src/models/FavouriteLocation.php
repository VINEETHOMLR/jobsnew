<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class FavouriteLocation extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "fav_location";

        $this->columns = [
            'id', 
            'location_name', 
            'user_id',
            'lat', 
            'longitude',  
            'status',  
            'created_at',  
            'updated_at',   
        ];
    }

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
     * @return Array
     */
    public static function attrs()
    {
        return  [
            'id', 
            'location_name', 
            'user_id',
            'lat', 
            'longitude',  
            'status',  
            'created_at',  
            'updated_at',   
        ];
    }
 

   public function getLocationDetail($userId){

        $response = $this->callSql("SELECT id,location_name,lat,longitude FROM $this->tableName WHERE user_id='$userId' AND status=1","rows");

        return $response;

    }  


   public function deleteLocation($userId){
        
        $time = time();

        $this->query("UPDATE $this->tableName SET status=3,updated_at='$time' WHERE id='$userId'");

        $this->execute();

        return true;

    }  
    
}
