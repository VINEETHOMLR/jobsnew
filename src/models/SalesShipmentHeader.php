<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class SalesShipmentHeader extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "sales_shipment_header";

        $this->columns = [
            'id', 
            'salesshipmentno', 
            'customernumber',
            'salesperson', 
            'contactperson',
            'contactnumber',
            'customername',
            'shiptoname',
            'salesordernumber',
            'shiptoaddress',
            'shiptoaddress2',
            'assigned_driver_id',
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
            'salesshipmentno', 
            'customernumber', 
            'salesperson',
            'contactperson',
            'contactnumber',
            'customername',
            'shiptoname',
            'salesordernumber',
            'shiptoaddress',
            'shiptoaddress2',
            'assigned_driver_id',
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


    public function addlog($data){
        
        $this->query("INSERT INTO post_log SET request='$data' ");
            $this->execute();
    }




    public function checkExist($salesshipmentno){

        
       $response = $this->callSql("SELECT * FROM $this->tableName WHERE  salesshipmentno = '$salesshipmentno'");


        if(!empty($response)) {
            
            return $response;
        }else{
            return [];
        }



    }

    public function getShipmentList($filter)
    {
        $where = " where id !=0 AND delivery_status = 2 AND status = 1";
        if(!empty($filter['startdate']) && !empty($filter['enddate'])) {
            
            $where .= " AND assigned_date BETWEEN $filter[startdate] AND $filter[enddate]";
        }
        if(!empty($filter['userId'])) {
            
            $where .= " AND assigned_driver_id = $filter[userId]";
        }


        $shipmentList = $this->callSql("SELECT * FROM $this->tableName $where ORDER BY id asc",'rows');
        $data = [];

        if(!empty($shipmentList)) {

            foreach($shipmentList as $k=>$v){
                
                $data[] = array(
                               'id'              => $v['id'],
                               'shipment_number' => $v['salesshipmentno'],
                               'customer_number' => $v['customernumber'],
                               'order_number'    => $v['salesordernumber'],
                             );
            }

        }
        
        return $data; 
        




    }





    public function getDetails($filter)
    {
        $where = " where id!=0 AND status = 1";
        if(!empty($filter['delivery_status'])) { 
            
            $where .= " AND delivery_status = $filter[delivery_status]";
        }
        if(!empty($filter['assigned_driver_id'])) { 
            
            $where .= " AND assigned_driver_id = $filter[assigned_driver_id]";
        }
        if(!empty($filter['id'])) { 
            
            $where .= " AND id = $filter[id]";
        }
        $shipmentDetails = $this->callSql("SELECT * FROM $this->tableName $where",'row');  
        $data = []; 
        if(!empty($shipmentDetails)) {
            $data = array(
                          'id'                 => $shipmentDetails['id'],
                          'shipement_number'   => $shipmentDetails['salesshipmentno'],
                          'customer_number'    => $shipmentDetails['customernumber'],
                          'sales_person'       => $shipmentDetails['salesperson'],
                          'contact'            => $shipmentDetails['contactperson'],
                          'customer_name'      => $shipmentDetails['customername'],
                          'order_number'       => $shipmentDetails['salesordernumber'],
                          'address1'           => $shipmentDetails['shiptoaddress'],
                          'address2'           => $shipmentDetails['shiptoaddress2'],
                         );
            return $data; 
        }
        return [];


    }


    public function update_shipment($data,$id)
    {

        $customerweightticket         = $data['customerweightticket'];
        $customersigneddo             = $data['customersigneddo'];          
        $geolocation                  = $data['geolocation'];
        $pictureurl1                  = $data['pictureurl1'];
        $pictureurl2                  = $data['pictureurl2'];
        $pictureurl3                  = $data['pictureurl3'];
        //$delivery_status              = $data['delivery_status'];
        $updated_at                   = time();
        

        $query = "UPDATE $this->tableName SET 
                                customerweightticket = :customerweightticket,
                                customersigneddo = :customersigneddo,
                                geolocation = :geolocation,
                                pictureurl1 = :pictureurl1,
                                pictureurl2 = :pictureurl2,
                                pictureurl3 = :pictureurl3,
                                updated_at = :updated_at  
                                WHERE id = :id";



        $this->query($query);

        $this->bind(':customerweightticket', $customerweightticket);
        $this->bind(':customersigneddo', $customersigneddo);
        $this->bind(':geolocation', $geolocation);
        $this->bind(':pictureurl1', $pictureurl1);
        $this->bind(':pictureurl2', $pictureurl2);
        $this->bind(':pictureurl3', $pictureurl3);
        $this->bind(':updated_at', $updated_at);
        $this->bind(':id', $id);
        return $this->execute();

    }


   public function GetShipmentfromQr($shipment_no)
    { 
        $where = " where id!=0";
        if(!empty($shipment_no)) { 
    
            $where .= " AND salesshipmentno = '$shipment_no'";
        }

        $shipmentDetails = $this->callSql("SELECT * FROM $this->tableName $where",'row');
    
        if(!empty($shipmentDetails)) {
           
            return $shipmentDetails; 
        }
        return [];


    }

    public function testlog(){
        $this->query("INSERT INTO `log` SET request='test'");
        $this->execute();
    }


    public function getListApi()
    {

        $shipmentHeaderList = $this->callSql("SELECT * FROM $this->tableName",'rows');
        $result= [];
        foreach($shipmentHeaderList as $k=>$v){

            $vehicle = $this->callSql("SELECT vehicle_number FROM driver_user WHERE id=$v[assigned_driver_id] ",'value');

            $line = [];
            $lineDetails = $this->callSql("SELECT * FROM sales_shipment_line WHERE sales_shipment_header_id=$v[id] ",'rows');
            $delivery_status_array = ['1'=>'Delivered','2'=>'Not Delivered'];
            foreach($lineDetails as $lineDetailList){
                
                $line[] = ['salesshipmentnumber'  => $lineDetailList['salesshipmentnumber'],
                           'linenumber'           => $lineDetailList['linenumber'],
                           'salesordernumber'     => $lineDetailList['salesordernumber'],
                           'itemnumber'           => $lineDetailList['itemnumber'],
                           'itemdescription'      => $lineDetailList['itemdescription'],
                           'unitofmeasurecode'    => $lineDetailList['unitofmeasurecode'],
                           'quantity'             => $lineDetailList['quantity'],
                           'shipmentdate'         => $lineDetailList['shipmentdate'],
                           'shipmenttime'         => $lineDetailList['shipmenttime'],
                           'customerweight'       => $lineDetailList['customerweight'],
                           'postingdate'          => $lineDetailList['postingdate'],
                           'postingdate'          => $lineDetailList['postingdate'],
                           'delivery_status'      => $lineDetailList['delivery_status'],
                           'delivery_status_text' => $delivery_status_array[$lineDetailList['delivery_status']]
                      


                           ];
            }
            
            $result[]  = array(
                               'salesshipmentno'       => $v['salesshipmentno'],
                               'customernumber'        => $v['customernumber'],
                               'salesperson'           => $v['salesperson'],
                               'contactperson'         => $v['contactperson'],
                               'contactnumber'         => $v['contactnumber'],
                               'customername'          => $v['customername'],
                               'shiptoname'            => $v['shiptoname'],
                               'salesordernumber'      => $v['salesordernumber'],
                               'shiptoaddress'         => $v['shiptoaddress'],
                               'shiptoaddress2'        => $v['shiptoaddress2'],
                               'customerweightticket'  => !empty($v['customerweightticket']) ? BASEURL.'web/upload/weight/'.$v['customerweightticket']:'',
                               'customersigneddo'      => !empty($v['customersigneddo']) ? BASEURL.'web/upload/sign/'.$v['customersigneddo']:'',
                               'geolocation'           => $v['geolocation'],
                               'pictureurl1'           => $v['pictureurl1'],
                               'pictureurl2'           => $v['pictureurl2'],
                               'pictureurl3'           => $v['pictureurl3'],
                               'vehicle'               => $vehicle,
                               'postingdate'           => $v['postingdate'],
                               'line'                  => $line
                              );
           // $result[]['line'] =array();
        }

        return $result;



    }
  
    
}
