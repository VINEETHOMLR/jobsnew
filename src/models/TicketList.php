<?php

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class TicketList extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db") {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "ticket_list";

        $this->columns = [
            'id', 
            'user_id', 
            'title', 
            'status',
            'created_at', 
            'created_by', 
            'created_ip',
        ];
    }

    /**
     *
     * @return Array
     */
    public static function attrs() {
        return  [
            'id', 
            'user_id', 
            'title', 
            'status',
            'created_at', 
            'created_by', 
            'created_ip',
            
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

  
    public function updateRecord($input_params,$where_params) {
        $this->assignAttrs($input_params);
        return $this->update($where_params);
       
    }
    public function getRecordNopk($input_attributes,$where_params,$result_type,$order_by,$order) {
         
                 $setAttrs = ' ';
        $whereAttr = '';
        foreach ($input_attributes as $k => $v) {
            $setAttrs.= $v.",";
        }
        
          if (!empty($where_params)) {
            $cnt = 1;
            $where = ' WHERE ';
            $flag=0;
            foreach ($where_params as $attr => $val) {
              if($flag==0)
              $where.=$attr."='".$val."'";
              else 
               $where.=" and ".$attr."='".$val."'";
              $flag=1;
            }
        }
        $ordersql='';
        if(trim($order_by)!=""&& trim($order)!="")
        $ordersql=' ORDER BY '.$order_by.' '.$order;
        
         $query= 'SELECT '  . rtrim($setAttrs,',') .' FROM ' .$this->tableName . ' ' . $where.$ordersql.' LIMIT 1';
         return $this->callSql($query,$result_type);
        
    }
    
    public function getRecordsNopk($input_attributes,$where_params,$start,$end,$order_by='',$order='') {
         
        $setAttrs = ' ';
        $whereAttr = '';
        foreach ($input_attributes as $k => $v) {
            $setAttrs.= $v.",";
        }
        
          if (!empty($where_params)) {
            $cnt = 1;
            $where = ' WHERE ';
            $flag=0;
            foreach ($where_params as $attr => $val) {
              if($flag==0)
              $where.=$attr."='".$val."'";
              else 
              $where.=" and ".$attr."='".$val."'";
              $flag=1;
            }
        }
        $ordersql=' ORDER BY '.$order_by.' '.$order;
        $limit='';
        if((trim($start)!="")&&(trim($end)!=""))
        $limit=' LIMIT '.$start.','.$end;
        $query= 'SELECT '  . rtrim($setAttrs,',') .' FROM ' .$this->tableName . ' ' . $where.$ordersql.$limit;
        return $this->callSql($query,'rows');
        
    }
    
    function __call($fun, $arg) {
        //echo $fun;
        $input_attributes = $arg[0]['input'];
        $where_params = $arg[0]['where'];
        $order_by='';
        $order='';
        $start='';
        $end='';
        
        if((isset($arg[0]['orderby']))&&(trim($arg[0]['orderby'])!=""))
        $order_by=$arg[0]['orderby'];
        
        if((isset($arg[0]['order']))&&(trim($arg[0]['order'])!=""))
        $order=$arg[0]['order'];
        
        if((trim($order_by)=="")||(trim($order)=="")) {
            $order_by='';
            $order='';
        }
        
        if((isset($arg[0]['start']))&&(trim($arg[0]['start'])!=""))
        $start=$arg[0]['start'];
        
        if((isset($arg[0]['end']))&&(trim($arg[0]['end'])!=""))
        $end=$arg[0]['end'];
        
        if((trim($start)=="")||(trim($end)=="")) {
            $start='';
            $end='';
        }
            
        if (trim(strtolower(substr($fun, -5))) == 'value')
            return $this->getRecordNopk($input_attributes, $where_params, 'value',$order_by,$order);
        if (trim(strtolower(substr($fun, -3))) == 'row')
            return $this->getRecordNopk($input_attributes, $where_params, 'row',$order_by,$order);
        if (trim(strtolower(substr($fun, -4))) == 'rows')
            return $this->getRecordsNopk($input_attributes,$where_params,$start,$end,$order_by,$order);
    }


    
    

   
     
    
}
