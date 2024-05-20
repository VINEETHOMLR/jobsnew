<?php

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;

class Coin extends Database
{

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "coin";

        $this->columns = [
            'id',                  
            'coin_name',             
            'coin_code',      
            'value',        
            'transfer_out_value',      
            'master_address', 
            'wallet_group',
            'status'           
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
                    'coin_name',             
                    'coin_code',      
                    'value',        
                    'transfer_out_value',      
                    'master_address', 
                    'wallet_group',
                    'status'           
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

	public function getAll()
    {
        $sql = 'SELECT * FROM '.$this->tableName.'  ';
        return $this->callSql($sql,'rows');
    }


    public function getCoinData()
    {
        $sql = 'SELECT id,coin_code,coin_name FROM '.$this->tableName.'  ';
        return $this->callSql($sql,'rows');
    }

    public function getByCoinCode($coin_code)
    {

        $sql = "SELECT * FROM $this->tableName WHERE coin_code = '$coin_code' ";

        $result = $this->callSql($sql,'row');

        if (empty($result)) {
        	$result = [];
        }
        
        return $result;
    }

    public function getCoinDetails(){
          
        $rows = $this->callSql("SELECT * FROM $this->tableName ","rows");

        if(empty($rows)){
          return [];
        }

        return $rows;

    }

        /** Public Function to Update Wallet Balance
     * @param VARCHAR $coinCode
     * @param DECIMAL $price_in
     * @param DECIMAL $price_out
     */
    public function updateCoinPrice($coinCode, $price_in, $price_out)
    {
        $this->query("UPDATE $this->tableName SET value='$price_in',transfer_out_value='$price_out' WHERE coin_code='$coinCode'");

        $this->execute();

        return true;
    }

    public function getCoinId($coinCode)
    {
        $coinId = $this->callSql("SELECT id FROM $this->tableName WHERE coin_code='$coinCode' LIMIT 1", 'value');
        return $coinId;
    }

    public function updateCoin($coin_id,$update = []) {    

        $query = [];

        foreach($update as $key => $value) {
           $query[] = " $key = '".$value."' ";
        }  

        $implode = '';
        if (!empty($query)) {
            $implode = implode(",",$query);

            $this->query("UPDATE $this->tableName SET $implode WHERE id = '$coin_id'  ");

            $this->execute();
        }

    }
	
	public function updateCoinbyparam($coincode,$update = []) {    

        $query = [];

        foreach($update as $key => $value) {
           $query[] = " $key = '".$value."' ";
        }  

        $implode = '';
        if (!empty($query)) {
            $implode = implode(",",$query);

            $this->query("UPDATE $this->tableName SET $implode WHERE coin_code='$coincode'  ");

            $this->execute();
        }

    }

   
}
?>