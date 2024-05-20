<?php

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;

class CoinWalletAddress extends Database
{

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

	public function __construct($db = 'db')
	{
		parent::__construct(Raise::params()[$db]);

		$this->tableName = 'coin_wallet_address';

        $this->columns =  [
                             'id',
                             'user_id',
                             'wallet_group',
                             'wallet_address',
                             'status',
                             'created_at',
                             'created_by',
                             'created_ip'
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
                    'wallet_group',
                    'wallet_address',
                    'status',
                    'created_at',
                    'created_by',
                    'created_ip'      
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

    public function insert($ip)
    {
        $ins_array = array();

        $ins_array[] = '\'\'';
        $ins_array[] = '\''.(!empty($ip['user_id'])?$ip['user_id']:0).'\'';
        $ins_array[] = '\''.(!empty($ip['wallet_group'])?$ip['wallet_group']:0).'\'';
        $ins_array[] = '\''.(!empty($ip['wallet_address'])?$ip['wallet_address']:"").'\'';
        $ins_array[] = '\''.(!empty($ip['status'])?$ip['status']:"1").'\'';
        $ins_array[] = '\''.(!empty($ip['created_at'])?$ip['created_at']:0).'\'';
        $ins_array[] = '\''.(!empty($ip['created_by'])?$ip['created_by']:0).'\'';
        $ins_array[] = '\''.(!empty($ip['created_ip'])?$ip['created_ip']:"").'\'';
        
        $ins_array = '('.implode(',', $ins_array).')';

        $sql = 'INSERT INTO '.$this->tableName.' ('.implode(',', $this->columns).') VALUES '.$ins_array;
        
        $this->query($sql);
        $this->execute();

        return true;
    }

	public function get($user_id,$ip,$type = 'row')
    {

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$user_id.'\'';

        if (!empty($ip['wallet_group'])) {
            $where_str_array[] = 'wallet_group = '.$ip['wallet_group'];
        }

        $select = '*';
        if (!empty($ip['select'])) {
            if (is_array($ip['select'])) {
                $select = implode(',', $ip['select']);
            } else {
                $select = $ip['select'];
            }
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' ';
        
        return $this->callsql($sql, $type);
    }

    public function checkUserFromAddress($walletAddress, $coinId)
    {
        $userId = $this->callSql("SELECT user_id FROM $this->tableName WHERE coin_id='$coinId' AND wallet_address='$walletAddress'", 'value');

        return $userId;
    }

}
?>