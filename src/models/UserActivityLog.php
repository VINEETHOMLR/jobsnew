<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class UserActivityLog extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_activity_log";

        $this->columns = [
            'user_id', 
            'admin_id', 
            'module',
            'action',
            'activity',
            'before_data',
            'after_data',
            'location',
            'browser',
            'device',
            'device_type',
            'device_os',
            'created_at',
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
            'user_id', 
            'admin_id', 
            'module',
            'action',
            'activity',
            'before_data',
            'after_data',
            'location',
            'browser',
            'device',
            'device_type',
            'device_os',
            'created_at',
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

    public function createRecord($data)
    {
        $this->assignAttrs($data);
        return $this->save();
    }    

    public function saveUserLog($ip){
       
        $userObj = Raise::$userObj;
        $user_id = !empty($userObj['id']) ? $userObj['id'] :'0';
        $insert['module'] = !empty($ip['module']) ? $ip['module']:"";
        $insert['action'] = !empty($ip['action']) ? $ip['action']:"";
        $insert['activity'] = !empty($ip['activity']) ? $ip['activity']:"";
        $userSystemInfo = H::getUserSystemInfo();
        $insert['user_id'] = !empty($ip['user_id']) ? $ip['user_id']: $user_id;
        $insert['location'] = $userSystemInfo['device_location'];
        $insert['device'] = $userSystemInfo['device_id'];
        $insert['device_type'] = $userSystemInfo['device_type'];
        $insert['device_os'] = $userSystemInfo['device_os'];
        $insert['created_at'] = time();
        $insert['created_ip'] = getClientIP();

        $insert['admin_id'] = '0';
        $insert['before_data'] = '';
        $insert['after_data'] = '';

        $this->createRecord($insert);

        

    }



    
    

    

    
   
    
}
