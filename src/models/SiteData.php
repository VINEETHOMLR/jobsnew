<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class SiteData extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "site_data";

        $this->columns = [
            'id', 
            'keyvalue', 
            'data',
            'updated_at',
			'updated_by',		
			'updated_ip',
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
            'keyvalue', 
            'data',
            'updated_at',
			'updated_by',		
			'updated_ip', 
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
		
		
        $response = $this->callSql("SELECT id,keyvalue,data FROM $this->tableName WHERE keyvalue IN('game_service','deposit_service','withdrawal_service','swap_service','financial_service')  ","rows");

        $rows = [];
		$enablearray = array("0"=>"false","1"=>"true");
        if (!empty($response)) {
                foreach ($response as $key => $info) {
                   
                    $rows[$key]['enabled'] = !empty($info['data'])?strval($enablearray[$info['data']]):'0';
                    $rows[$key]['key'] = !empty($info['keyvalue'])?strval($info['keyvalue']):'-';
                    }
        }	
        return $rows;
    }


    public function getRecord($key=''){
        
        $where = "";
        if(!empty($key)) {
            
            $where = "where keyvalue='$key'";

        }

        

        $response = $this->callSql("SELECT * FROM $this->tableName $where ","row");

        if(!empty($response)) {
            return $response;
        }
        return [];

    }
   
    

    public function getSiteData($key_name="")
    {

        if (!empty($key_name)) {
            $sql = "SELECT data FROM $this->tableName WHERE keyvalue = '".$key_name."' LIMIT 1 ";

            $result = $this->callSql($sql,"value");

        } else {

            $sql = "SELECT keyvalue,data FROM $this->tableName";

            $rows = $this->callSql($sql,"rows");

            if (!empty($rows)) {
                $result = array_column($rows, "data", "keyvalue");
            } else {
                $result = [];
            }
        }

        return $result;
    }

    
   
    
}
