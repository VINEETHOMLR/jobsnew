<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;
use src\models\Notification;


class Jobs extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "job_post";

        $this->columns = [
            'id', 
            'title', 
            'latitude',
            'longitude',  
            'location',
            'description',
            'status',
            'category_id',
            'jobseeker_id',
            'created_at',
            'updated_at',
            'labour_cost',
            'material_cost',
            'total_amount',
            'payment_status ',
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
            'title', 
            'latitude',
            'longitude',  
            'location',
            'description',
            'status',
            'category_id',
            'jobseeker_id',
            'created_at',
            'updated_at',
            'labour_cost',
            'material_cost',
            'total_amount',
            'payment_status ',
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



    function insertRecord($params){

        $query = "INSERT INTO $this->tableName (`title`,`user_id`,`latitude`,`longitude`,`location`,`images`,`description`,`status`,`category_id`,`created_at`) VALUES (:title,:user_id,:latitude,:longitude,:location,:images,:description,:status,:category_id,:created_at)";
            $this->query($query);
            $this->bind(':title',       $params['title']);
            $this->bind(':user_id',     $params['user_id']);
            $this->bind(':latitude',    $params['latitude']);
            $this->bind(':longitude',   $params['longitude']);
            $this->bind(':location',    $params['location']);
            $this->bind(':images',      $params['images']);
            $this->bind(':description', $params['description']);
            $this->bind(':status',      1);
            $this->bind(':category_id', $params['category_id']);
            $this->bind(':created_at',  time());
           
            if($this->execute()){

                $lid = $this->lastInsertId();


                $rows = $this->callsql("SELECT user_id FROM `user_extra` WHERE  FIND_IN_SET($params[category_id],category )  ",'rows');
                $data = json_encode(['data'=>"New Job Posted ".$params['title'],'id'=>$lid]);

                foreach($rows as $value)
                {
                     (new Notification)->insertNotification($value['user_id'],$data,1);
                } 

            return true;
            
            }
            return false;
            
    }  

    function updateRecord($params){

        $query = " UPDATE  $this->tableName SET  `status`=:status,`jobseeker_id`=:jobseeker_id,`updated_at`=:updated_at  WHERE id=:post_id ";
            $this->query($query);
            $this->bind(':status',        2);
            $this->bind(':jobseeker_id',  $params['applicant_id']);
            $this->bind(':updated_at',    time());
            $this->bind(':post_id',       $params['post_id']);
           
            if($this->execute()){
                return true;
            }
            return false;
            
    } 

    public function getOpenOrders($params)
    {
        $where = " WHERE id!='0' AND status IN(1) ";
        if(!empty($params['user_id'])) {

            $where .= " AND user_id='$params[user_id]'";

        }

        $sql = "SELECT * FROM $this->tableName $where ORDER BY id DESC";

        
        $rows = $this->callsql($sql,"rows");
        $resp = [];
        if(!empty($rows)) {

            foreach($rows as $index=>$value){
                
                $images = json_decode($value['images'],true);
                foreach ($images as $key => $image) {
                    $images[$key] =  BASEURL.'web/upload/images/'.$image;
                }
                $category = $this->callsql("SELECT name FROM category WHERE id='$value[category_id]'",'value');
                $resp[] = array(
                                'id'                  => $value['id'],
                                'title'               => $value['title'],
                                'service_category'    => $category,
                                'images'              => $images,
                                'status'              => $value['status']
                               ); 
            }
            //$totalPages = floor($getTotal/$perPage); 
            //if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 

        }

       

        $recordsFiltered = count($resp);

       
        $datarray['orderList']['recordsTotal']      = !empty($recordsFiltered)?strval($recordsFiltered):'0';
        //$datarray['game_list']['recordsFiltered']   = !empty($resp)?strval($recordsFiltered):'0';
        //$datarray['game_list']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        //$datarray['game_list']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['orderList']['recordsList']       = !empty($resp) ? $resp :[];

        return $datarray;

    }

    public function getPastOrders($params)
    {
        $where = " WHERE id!='0' AND status IN(2,3,4) ";
        if(!empty($params['user_id'])) {

            $where .= " AND user_id='$params[user_id]'";

        }

        $sql = "SELECT * FROM $this->tableName $where ORDER BY id DESC";

        
        $rows = $this->callsql($sql,"rows");
        $resp = [];

        $statusArray = ['1'=>'Posted','2'=>'Confirmation Pending','3'=>'Jobseeker Accepted','4'=>'Completed'];
        if(!empty($rows)) {

            foreach($rows as $index=>$value){
                
                $images = json_decode($value['images'],true);
                foreach ($images as $key => $image) {
                    $images[$key] =  BASEURL.'web/upload/images/'.$image;
                }
                $category     = $this->callsql("SELECT name FROM category WHERE id='$value[category_id]'",'value');
                $hired_person = $this->callsql("SELECT name FROM user WHERE id='$value[jobseeker_id]'",'value');
                $amount_paid = $value['payment_status'] == '1' ? $value['total_amount'] : '-';
                $resp[] = array(
                                'id'                  => $value['id'],
                                'title'               => $value['title'],
                                'service_category'    => $category,
                                'images'              => $images,
                                'status'              => $value['status'],
                                'status_text'         => $statusArray[$value['status']], 
                                'hired_person'        => $hired_person,
                                'amount_paid'         => $amount_paid
                               ); 
            }
            //$totalPages = floor($getTotal/$perPage); 
            //if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 

        }

       

        $recordsFiltered = count($resp);

       
        $datarray['pastOrderList']['recordsTotal']      = !empty($recordsFiltered)?strval($recordsFiltered):'0';
        //$datarray['game_list']['recordsFiltered']   = !empty($resp)?strval($recordsFiltered):'0';
        //$datarray['game_list']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        //$datarray['game_list']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['pastOrderList']['recordsList']       = !empty($resp) ? $resp :[];

        return $datarray;

    }


    public function applyJob($params)
    {

        $post_id = $params['post_id'];
        $status  = $params['status'];
        $jobseeker_id  = $params['jobseeker_id'];
        $action = $params['action'];
        
        $updated_at = time();
        $sql = "UPDATE job_post SET jobseeker_id='$jobseeker_id',status='$status',updated_at='$updated_at' WHERE id='$post_id'";

        $this->query($sql);
        $result = $this->execute();

        $application_status = $action == '1' ? '4' : '2';


        $sql = "UPDATE applications SET status='$application_status',updated_at='$updated_at' WHERE post_id ='$post_id' ORDER BY id DESC LIMIT 1";

        $this->query($sql);
        $result = $this->execute();

        return $result;

    }

    
   
    
}
