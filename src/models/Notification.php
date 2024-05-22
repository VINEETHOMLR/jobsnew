<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Notification extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "notification";

        $this->columns = [
            'id', 
            'user_id', 
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
            'user_id', 
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


    function insertNotification($user_id,$data,$type){

        $query = "INSERT INTO `notification` (`user_id`,`data`,`type`,`status`,`created_at`) VALUES (:user_id,:data,:type,:status,:created_at)";
            $this->query($query);
            $this->bind(':user_id',       $user_id);
            $this->bind(':data',          $data);
            $this->bind(':type',          $type);
            $this->bind(':status',        0);
            $this->bind(':created_at',    time());
           
            if($this->execute()){
                return true;
            }
            return false;
            
    }  

    public function getNotifications($filter){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $user_id = !empty($filter['user_id']) ? $filter['user_id'] : '0';

        array_push($where_str_array,"  user_id =  ".$user_id." "); 

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $select = ' id,user_id,data,type';
       
        $limit = ' ';

        $orderby = ' ORDER BY id DESC ';
        

        $getTotal = $this->callsql('SELECT count(id) FROM notification  WHERE '.$where_str.' ','value');

        $sql = 'SELECT  '.$select.'  
        FROM 
            notification 
        WHERE 
            '.$where_str.'  '.$orderby.' '.$limit.' ';

       
        $rows = $this->callsql($sql,"rows");
        $resp = [];
        if(!empty($rows)) {

            foreach($rows as $index=>$value){
                
                $datas  = json_decode($value['data'],true);
               
                $resp[] = array(
                                'id'         => $value['id'],
                                'message'    => $datas['data'],
                                'data_id'    => $datas['id'],
                                'type'       => $value['type']
                               ); 
            }
            //$totalPages = floor($getTotal/$perPage); 
            //if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 

        }


        $recordsFiltered = count($resp);

       
        $datarray['game_list']['recordsTotal']      = !empty($recordsFiltered)?strval($recordsFiltered):'0';
        //$datarray['game_list']['recordsFiltered']   = !empty($resp)?strval($recordsFiltered):'0';
        //$datarray['game_list']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        //$datarray['game_list']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['game_list']['recordsList']       = !empty($resp) ? $resp :[];

        return $datarray;

    }

    
}
