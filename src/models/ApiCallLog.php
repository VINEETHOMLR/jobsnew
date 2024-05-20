<?php

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;

/**
 * @property int(10) $id
 * @property varchar(20) $name
 * @property varchar(20) $description
 * @property int(10) $status
 * */
class ApiCallLog extends Database
{

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "api_call_log";

        $this->columns = [
                            'id',               
                            'user_id',          
                            'log_type',         
                            'api_header',       
                            'api_method',       
                            'request_data_type',
                            'raw_request',      
                            'api_request_param',
                            'api_response',     
                            'is_success',       
                            'created_at',       
                            'created_by',       
                            'created_ip',       
                            'updated_at',       
                            'updated_by',       
                            'updated_ip'     
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
                    'user_id',          
                    'log_type',         
                    'api_header',       
                    'api_method',       
                    'request_data_type',
                    'raw_request',      
                    'api_request_param',
                    'api_response',     
                    'is_success',       
                    'created_at',       
                    'created_by',       
                    'created_ip',       
                    'updated_at',       
                    'updated_by',       
                    'updated_ip'     
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



    public function insertLog($coinId, $userId = 0, $type, $request)
    {
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        $this->query("INSERT INTO $this->tableName SET coin_id='$coinId',user_id='$userId',log_type='$type',
                            raw_request='$request',request_time='$time',request_ip='$ip',created_at='$time',created_ip='$ip' ");

        $this->execute();

        return $this->lastInsertId();
    }

    public function updateLog($insId, $response, $formatResponse)
    {
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        $this->query("UPDATE $this->tableName SET api_response='$response',formatted_response='$formatResponse',
                        response_time='$time',updated_at='$time',updated_ip='$ip' WHERE id='$insId'");

        $this->execute();

        return true;
    }

   
}
