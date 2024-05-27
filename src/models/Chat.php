<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Chat extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "chat";

        $this->columns = [
            'id', 
            'post_id', 
            'employer_id',
            'jobseeker_id', 
            'created_at' 
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
            'post_id', 
            'employer_id',
            'jobseeker_id', 
            'created_at' 
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

    public function CreateChat($params){

        $post_id          = $params['post_id'];
        $jobseeker_id     = $params['jobseeker_id'];
        $employer_id      = $params['employer_id'];

        $chatid  = $this->callsql("SELECT id FROM $this->tableName WHERE post_id='$post_id' AND employer_id='$employer_id' AND jobseeker_id='$jobseeker_id'",'value');
        $time = time();
        if(empty($chatid)) {
            
            $sql = "INSERT INTO $this->tableName SET post_id='$post_id',employer_id='$employer_id',jobseeker_id='$jobseeker_id',created_at='$time'";
            $this->query($sql);
            $this->execute();
            $chatid = $this->lastInsertId();

        }

        return $chatid;



       
    }  

    public function getMessages($params)
    {

        $chat_id= $params['chat_id'];
        $sql = "SELECT * FROM chat_messages WHERE chat_id = '$chat_id' ORDER BY id ASC";
        $list = $this->callsql($sql,'rows');


        
        $result = [];
        foreach($list as $key=>$value)
        {
            
            $sender_name = $this->callsql("SELECT name FROM user WHERE id='$value[from_id]'",'value');
            $receiver_name = $this->callsql("SELECT name FROM user WHERE id='$value[to_id]'",'value');
            $result[$key]['id'] = $value['id'];
            $result[$key]['sender_name'] = $sender_name;
            $result[$key]['receiver_name'] = $receiver_name;
            $result[$key]['sender_id'] = $value['from_id'];
            $result[$key]['to_id'] = $value['to_id'];
            $result[$key]['message'] = $value['message'];



        }

        return $result;

       

    }

    public function getChatList($params)
    {
        
        $where = " WHERE id!='0' ";
        $where2 = " WHERE id!='0'";
        $user_id = $params['user_id'];
        $type = $params['type'];

        if(!empty($params['search_keyword'])) {
            $where2 .= " AND name LIKE '%$data[search_keyword]%' "; 
        }
        if($type == '1') { //employer
            
            $where2 .= " AND role_id='2'";
            $sql = "SELECT id FROM user $where2";
            $userIds = $this->callsql($sql,'rows');
            $where .= " AND employer_id='$user_id'";
            if(!empty($userIds)) {

                $userIds = array_column($userIds,'id');
                $userIds = implode(',',$userIds);
                $where .= " AND jobseeker_id IN($userIds)";

            }


            

        }
        if($type == '2') { //jobseeker
            
            $where2 .= " AND role_id='1'";
            $sql = "SELECT id FROM user $where2";
            $userIds = $this->callsql($sql,'rows');
            $where .= " AND jobseeker_id='$user_id'";
            if(!empty($userIds)) {

                $userIds = array_column($userIds,'id');
                $userIds = implode(',',$userIds);
                $where .= " AND employer_id IN($userIds)";

            }
            

        }
        $sql = "SELECT * FROM chat $where ORDER BY id DESC ";

        $list = $this->callsql($sql,'rows');
        $result = [];
        foreach($list as $key=>$value)
        {
            if($type == '1') {

                $name = $this->callsql("SELECT name FROM user WHERE id='$value[jobseeker_id]'",'value');

            }
            if($type == '2') {

                $name = $this->callsql("SELECT name FROM user WHERE id='$value[employer_id]'",'value');

            }

            $chatid = $value['id'];
            $lastMessage = $this->callsql("SELECT message FROM chat_messages WHERE id='$chatid' ORDER BY id DESC LIMIT 1");
            $lastMessage = !empty($lastMessage) ? $lastMessage : ''; 
            $result[$key]['chat_id'] = $value['id'];
            $result[$key]['name']    = $name;
            $result[$key]['message']    = $lastMessage;

        }

       return $result;
        


    }

    public function sendMessage($params)
    {

        $chat_id   = !empty($params['chat_id']) ? $params['chat_id'] : '';
        $from_id   = !empty($params['from_id']) ? $params['from_id'] : '';
        $to_id     = !empty($params['to_id']) ? $params['to_id'] : '';
        $message   = !empty($params['message']) ? $params['message'] : '';
        $created_at = time();

        $sql = "INSERT INTO chat_messages SET chat_id='$chat_id',from_id='$from_id',to_id='$to_id',message='$message',created_at='$created_at'";
        $this->query($sql);
        return $this->execute();

    }


    


    
    

    
   
    
}
