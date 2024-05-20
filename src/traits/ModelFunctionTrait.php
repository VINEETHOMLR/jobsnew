<?php

namespace src\traits;

trait ModelFunctionTrait
{

        public function createRecord($data) {
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
    public function getRecordsNopk($input_attributes,$where_params,$start,$end,$order_by='',$order='',$page,$perpage=10,$enable_pagination=FALSE) {
        
        if($enable_pagination) {
       // $perpage    = 10;
        $start      =($page==1)?0:($page-1)*$perpage;
        $end        = $perpage+1;
      //  $end        =3;
        }
        
        
        
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
        
        
        
        if($enable_pagination) {
        $countquery= 'SELECT count(id) FROM ' .$this->tableName . ' ' . $where.$ordersql;
        $count= $this->callSql($countquery,'value');
        $data= $this->callSql($query,'rows');
        
        $totalPages=round($count/$perpage)+(($count%$perpage)==0?0:1);
        if(!empty($data)) {
        $rows['last']=false;
        if(count($data)!=$end) {
        $rows['last']=true;
        
        }
        else {    
        array_pop($data);
        }
        
        $rows['data']=$data;
        $rows['recordsFiltered']=count($data);
        }
        else {
        $rows['last']=TRUE; 
        $rows['recordsFiltered']=0;
        $rows['data']=[];
        }
        $rows['recordsTotal']=$count;
        $rows['totalPages']=$totalPages;
        $rows['currentPage']=$page;
        $rows['perpage']=$perpage;
        
        $rows['recordsTotal']    =strval($rows['recordsTotal']); 
        $rows['recordsFiltered'] =strval($rows['recordsFiltered']); 
        $rows['totalPages']      =strval($rows['totalPages']); 
        $rows['currentPage']     =strval($rows['currentPage']); 
        $rows['perpage']         =strval($rows['perpage']); 
        return $rows;
        }
        
       else 
        return $this->callSql($query,'rows');
       
        
    }
    
         public function getRecordsNopk_old($input_attributes,$where_params,$start,$end,$order_by='',$order='',$page,$enable_pagination=FALSE) {
        
        if($enable_pagination) {
        $perpage    = 10;
        $rows['perpage']="10"; 
        $start      =($page==1)?0:($page-1)*$perpage;
        $end        = $perpage+1;
      //  $end        =3;
        }
        
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
        
        if($enable_pagination) {
        $data= $this->callSql($query,'rows');
        
        if(!empty($data)) {
        $rows['last']=false;
        if(count($data)!=$end)
        $rows['last']=true;
        else
        array_pop($data);
        $rows['data']=$data;
        
        }
        else {
        $rows['last']=TRUE; 
        $rows['data']=[];
        }
        return $rows;
        }
       else 
        return $this->callSql($query,'rows');
       
        
    }
}