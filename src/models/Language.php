<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Language extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "language";

        $this->columns = [
            'id', 
            'lang_name', 
            'lang_code',
            'status',
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
            'lang_name', 
            'lang_code',
            'status', 
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


    public function getData(){

		$sql = $where_str = $select = '';
        $where_str_array = array();

        
        $select = '*';
		
		
        $response = $this->callSql("SELECT * FROM $this->tableName WHERE  status=1 ","rows");

        $rows = [];
		
        if (!empty($response)) {
                foreach ($response as $key => $info) {
                   
                    $rows[$info['lang_code']] = !empty($info['lang_name'])?strval($info['lang_name']):'-';
                    }
        }	

        return $rows;
    }
   
    

    

    
   
    
}
