<?php

/**
 * @author 
 * @desc To describe an example of Model
 */

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;

/**
 * @author 
 */
class GameList extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "game_list";

        $this->columns = [
            'id',                  
            'name',             
            'image_url',      
            'game_code',        
            'game_vendor',      
            'order_num', 
            'is_hot_game',
            'status',       
            'created_at',  
            'created_by', 
            'created_ip',          
            'updated_at',          
            'updated_by',          
            'updated_ip',          
            'deleted_at',          
            'deleted_by',              
            'deleted_ip'             
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
                    'name',             
                    'image_url',      
                    'game_code',        
                    'game_vendor',      
                    'order_num', 
                    'is_hot_game',
                    'status',       
                    'created_at',  
                    'created_by', 
                    'created_ip',          
                    'updated_at',          
                    'updated_by',          
                    'updated_ip',          
                    'deleted_at',          
                    'deleted_by',              
                    'deleted_ip' 
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

    

    

    public function getRecords($ip,$filter){

        $sql = $where_str = $select = '';
        $where_str_array = array();
        $status = !empty($filter['status']) ? $filter['status'] : '';

        
        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }

        if(!empty($status)) {
            
            array_push($where_str_array,"  status =  ".$status." ");  
        }

        
        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $select = '*';
        if (!empty($ip)) {
            if (is_array($ip)) {
                $select = implode(',', $ip);
            } else {
                $select = $ip;
            }
        }
       
        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;



        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' WHERE '.$where_str.' ','value');


        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.'  ORDER BY order_num ASC '.$limit.'  ';

       
        $rows = $this->callsql($sql,"rows");
        $resp = [];
        if(!empty($rows)) {

            foreach($rows as $index=>$value){
                
                $gamePlayUrl = ""; 
                
                if(!empty($value['game_vendor']) && !empty($value['game_code'])) {
                    $gamePlayUrl = BASEURL .'game/play/?vendor='.$value['game_vendor'].'&code='.$value['game_code'];
                }
                
               
                $resp[] = array('game_name' => $value['name'],
                                'game_image' => !empty($value['image_url']) ? BASEURL.'bo/web/upload/game/'.$value['image_url'] : '',
                                'game_play_url' => $gamePlayUrl,
                                'is_hot_game'=>$value['is_hot_game']
                               ); 


            }
            $totalPages = floor($getTotal/$perPage); 
            if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 

        }else{
            $marketHistory = [];
            $resp = array();
            for($i=0;$i<$perPage;$i++){
                
                $recode = array('game_name'=>'1001','game_image'=>BASEURL.'web/img/games/1001game.jpg',"game_play_url"=>BASEURL."game/play/?vendor=SG&code=xyz1121", "is_hot_game"=>"1");
                //print_r($recode);exit;
                array_push($resp,$recode);
            }
            //print_r($resp);exit;
            /*$resp[0]['game_name'] = '1001';
            $resp[0]['game_image'] = "http://demotestivps.com/infinite_app/web/img/games/1001game.jpg";
            $resp[0]['game_play_url'] = "http://demotestivps.com/infinite_app/game/play/?vendor=SG&code=xyz1121";
            $resp[0]['is_hot_game'] = "1";
            $resp[1]['game_name'] = '1002';
            $resp[1]['game_image'] = "http://demotestivps.com/infinite_app/web/img/games/1003game.jpg";
            $resp[1]['game_play_url'] = "http://demotestivps.com/infinite_app/game/play/?vendor=SG&code=xyz1122";
            $resp[1]['is_hot_game'] = "0";
            $resp[2]['game_name'] = '1003';
            $resp[2]['game_image'] = "http://demotestivps.com/infinite_app/web/img/games/1003game.jpg";
            $resp[2]['game_play_url'] = "http://demotestivps.com/infinite_app/game/play/?vendor=SG&code=xyz1123";
            $resp[2]['is_hot_game'] = "1";


            $resp[3]['game_name'] = '1004';
            $resp[3]['game_image'] = "http://demotestivps.com/infinite_app/web/img/games/1004game.jpg";
            $resp[3]['game_play_url'] = "http://demotestivps.com/infinite_app/game/play/?vendor=SG&code=xyz1124";
            $resp[3]['is_hot_game'] = "1";

            $resp[4]['game_name'] = '1005';
            $resp[4]['game_image'] = "http://demotestivps.com/infinite_app/web/img/games/1005game.jpg";
            $resp[4]['game_play_url'] = "http://demotestivps.com/infinite_app/game/play/?vendor=SG&code=xyz1125";
            $resp[4]['is_hot_game'] = "1";*/

            $totalPages = '1';
            $getTotal = count($resp);
        }

        $recordsFiltered = count($resp);

       
        $datarray['game_list']['recordsTotal']      = !empty($getTotal)?strval($getTotal):'0';
        $datarray['game_list']['recordsFiltered']   = !empty($resp)?strval($recordsFiltered):'0';
        $datarray['game_list']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        $datarray['game_list']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['game_list']['recordsList']       = !empty($resp) ? $resp :[];

        return $datarray;

    }

    


}
