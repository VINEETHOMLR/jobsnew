<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class SalesShipmentLine extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "sales_shipment_line";

        $this->columns = [
            'id', 
            'sales_shipment_header_id', 
            'salesshipmentnumber',
            'linenumber', 
            'salesordernumber',
            'itemnumber',
            'itemdescription',
            'unitofmeasurecode',
            'quantity',
            'shipmentdate',
            'shipmenttime',
            'customerweight',
            'delivery_status',
            'postingdate'

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
            'sales_shipment_header_id', 
            'salesshipmentnumber', 
            'linenumber',
            'salesordernumber',
            'itemnumber',
            'itemdescription',
            'unitofmeasurecode',
            'quantity',
            'shipmentdate',
            'shipmenttime',
            'customerweight',
            'delivery_status',
            'postingdate'

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
        if($this->save()){
            return $this->lastInsertId();
        }
        return false;

    }


    public function updateRecord($input_params,$where_params) {
       
        $this->assignAttrs($input_params);
        return $this->update($where_params);
       
    }




    

    public function getList($sales_shipment_header_id){

        
       $response = $this->callSql("SELECT * FROM $this->tableName WHERE  sales_shipment_header_id = '$sales_shipment_header_id' ORDER BY id asc","rows");


        if(!empty($response)) {
            
            return $response;
        }else{
            return [];
        }



    }


    public function getRecords($filter)
    {
        $where = " where id!=0";

        if(!empty($filter['sales_shipment_header_id'])) {
            $where .= " AND sales_shipment_header_id = $filter[sales_shipment_header_id]";
        }
        if(!empty($filter['shipmentdate'])) {
            $where .= " AND shipmentdate = '$filter[shipmentdate]'";
        }
        if(!empty($filter['delivery_status'])) {
            $where .= " AND delivery_status = '$filter[delivery_status]'";
        }

        $salesLineList = $this->callSql("SELECT * FROM $this->tableName $where ORDER BY id asc","rows");
        $data = [];
        if(!empty($salesLineList)) {

            foreach($salesLineList as $k=>$v){
                
                $data[] = array(  
                                  'sales_id'        => $v['id'],
                                  'shipment_number' => $v['salesshipmentnumber'],
                                  'order_number'    => $v['salesordernumber'],
                                  'item_number'     => $v['itemnumber'],
                                  'line_number'     => $v['linenumber'],
                                  'description'     => $v['itemdescription'],
                                  'uom'             => $v['unitofmeasurecode'],
                                  'quantity'        => $v['quantity'],
                               );   
            }

            return $data;

        }else{
            return [];
        }

    }

    public function update_sales($data)
    {
        
        $time = date('H:i:s');
        foreach($data as $k=>$v){

            $input_params = [];
            $input_params['customerweight']  = $v['customerweight'];
            $input_params['delivery_status'] = '1';
            //$input_params['shipmentdate']   = date('Y-m-d');
            //$input_params['shipmenttime']   = $time;
            $where_params = [];
            $where_params['id'] = $v['sales_id'];
            $this->updateRecord($input_params,$where_params);
        
        }

        return true;

    }

    public function getShipmentList($filter =[]){
    

        $where = " where id!=0 AND delivery_status=2";

        if(!empty($filter['shipmentdate'])) {
            $where .= " AND postingdate = '$filter[shipmentdate]'";
        }

        $salesLineList = $this->callSql("SELECT DISTINCT(sales_shipment_header_id) FROM $this->tableName $where ORDER BY id DESC","rows");
        $data = [];

        if(!empty($salesLineList)) {
            
            foreach($salesLineList as $k=>$v){
                $sales_shipment_header_id = $v['sales_shipment_header_id'];
                $salesHeaderDetails = $this->callSql("SELECT * FROM sales_shipment_header WHERE id=$sales_shipment_header_id","row");
                
                if(($salesHeaderDetails['assigned_driver_id'] == $filter['driver_id']) && $salesHeaderDetails['status']==1) {
                    
                    $data[] = array('id'=>$salesHeaderDetails['id'],'shipment_number'=>$salesHeaderDetails['salesshipmentno'],'customer_number'=>$salesHeaderDetails['customernumber'],'order_number'=>$salesHeaderDetails['salesordernumber']); 
                }


            }
        }

        return $data;


    }




    

    
   
    
}
