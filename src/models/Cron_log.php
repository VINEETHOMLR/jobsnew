<?php

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;

class Cron_log extends Database
{

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "cron_log";

        $this->columns = [
            'id',                  
            'starttime',             
            'endtime',      
            'lock_status',        
                      
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
                    'starttime',             
                    'endtime',      
                    'lock_status',          
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

	public function getAll($params = [])
    {
        $where = ' WHERE id!=0';
        if(!empty($params['lock_status'])) {
            
            $where .= ' AND lock_status='.$params['lock_status'].'';
        }
        $sql = 'SELECT * FROM '.$this->tableName.$where.'';
        return $this->callSql($sql,'rows');
    }


    

    public function updateLog($id,$update = []) {    

        $query = [];

        foreach($update as $key => $value) {
           $query[] = " $key = '".$value."' ";
        }  

        $implode = '';
        if (!empty($query)) {
            $implode = implode(",",$query);

            $this->query("UPDATE $this->tableName SET $implode WHERE id = '$id'  ");

            $this->execute();
        }

    }
	
	

   
}
?>