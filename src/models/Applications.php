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
class Applications extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "applications";

        $this->columns = [
            'id',                  
            'post_id',             
            'user_id',      
            'status',        
            'basic_price',      
            'location', 
            'reach_time',
            'created_at',       
            'updated_at' 
                       
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
                    'user_id',      
                    'status',        
                    'basic_price',      
                    'location', 
                    'reach_time',
                    'created_at',       
                    'updated_at'
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

    

    public function getRecords($filter,$sort){


        $sql = $where_str = $select = '';
        $where_str_array = array();
        $status = !empty($filter['status']) ? $filter['status'] : '';
        $post_id = !empty($filter['post_id']) ? $filter['post_id'] : '0';

        
        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }

        array_push($where_str_array,"  ap.post_id =  ".$post_id." "); 

        if(!empty($status)) {
            
            array_push($where_str_array,"  ap.status =  ".$status." ");  
        }

        
        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $select = ' ap.post_id,ap.user_id,ap.status,ap.basic_price,ap.location,ap.reach_time,ap.created_at ';
        
       
        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $orderarray = [];

        if(!empty($sort))
        {
            foreach($sort as  $order)
            {

                array_push($orderarray," ".$order." "); 
            }

            $orderby = implode(' , ', $orderarray);

            if($orderby!="")
                $orderby = ' ORDER BY '.$orderby;
           
        }
        else
        {

            $orderby = ' ORDER BY ap.id DESC ';
        }


        //$orderby = ' ORDER BY ap.id ASC ';


        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' as ap WHERE '.$where_str.' ','value');


        $sql = 'SELECT '.$select.', ue.latitude AS seeker_lt, ue.longitude AS seeker_longi, ue.rating,
            (6371 * ACOS(COS(RADIANS('.$filter['latitude'].'))
            * COS(RADIANS(ue.latitude))
            * COS(RADIANS(ue.longitude) - RADIANS('.$filter['longitude'].'))
            + SIN(RADIANS('.$filter['latitude'].'))
            * SIN(RADIANS(ue.latitude)))) AS distance
        FROM 
            applications AS ap
        JOIN 
            user_extra AS ue ON ue.user_id = ap.user_id 
        WHERE 
            '.$where_str.' HAVING distance < 5  '.$orderby.' '.$limit.' ';

       
        $rows = $this->callsql($sql,"rows");
        $resp = [];
        if(!empty($rows)) {

            foreach($rows as $index=>$value){
                
                $name = $this->callsql("SELECT name FROM  user  WHERE id=$value[user_id] ",'value');
               
                $resp[] = array('name'    => $name,
                                'rating'  => $value['rating'],
                                'price'   => $value['basic_price'],
                                'distance'=>$value['distance'],
                                'status'  =>$value['status']
                               ); 



            }
            $totalPages = floor($getTotal/$perPage); 
            if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 

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
