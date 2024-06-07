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
use src\lib\Razorpay;


class Order extends Database
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



    function addCreateOrder($params)
    {

        $query = "INSERT INTO `order_details` (`post_id`,`amount`,`status`,`receipt_id`,`created_at`) VALUES (:post_id,:amount,:status,:receipt_id,:created_at)";
            $this->query($query);
            $this->bind(':post_id',    $params['post_id']);
            $this->bind(':amount',     $params['amount']);
            $this->bind(':status',    0);
            $this->bind(':receipt_id',   "order_".$params['post_id']);
            $this->bind(':created_at',   time());

            $this->execute();

            $orderid = $this->lastInsertId();

            $data['amount']   = $params['amount'];
            $data['post_id']  = $params['post_id'];
            $data['user_id']   = $params['user_id'];
            $data['amount']   = $params['amount'];
            $data['amount']   = $params['amount'];
            $data['receipt']  = "order_".$params['post_id'];
            
            $response = (new Razorpay)->createOrder($data);

            if($response['status']==true)
            {

                $query = " UPDATE  `order_details` SET  `status`=:status,`transaction_id`=:transaction_id,`response`=:response,`updated_at`=:updated_at  WHERE id=:id ";
                $this->query($query);
                $this->bind(':status',        1);
                $this->bind(':transaction_id',  $response['order_id']);
                $this->bind(':response',        $response['response']);
                $this->bind(':updated_at',      time());
                $this->bind(':id',              $orderid);

                $this->execute();

                return $response['order_id'];
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
