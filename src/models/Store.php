<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;
use src\lib\mailer\Mailer;


class Store extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "store";

        $this->columns = [
                            'id',                       
                            'user_id',                 
                            'image',                 
                            'product_id',             
                            'store_id',                 
                            'created_at',                     
                            'updated_at',                   
                            'type',         
                            'admin_remark',          
                            'is_accepted'  
                        ];
    }

    /**
     *
     * @return Array
     */
    public static function attrs()
    {
        return   [
                            'id',                       
                            'user_id',                 
                            'image',                 
                            'product_id',             
                            'store_id',                 
                            'created_at',                     
                            'updated_at',                   
                            'type',         
                            'admin_remark',          
                            'is_accepted'           
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

   
 
    public function getStore($params)
    {

         

        $lang  = $params['language'];
        $radius   = !empty($params['radius']) ? $params['radius'] :'5';
        $lat   = !empty($params['latitude']) ? $params['latitude'] :'';
        $long  = !empty($params['longitude']) ? $params['longitude'] :'';
        $where = " WHERE st.id!='0' AND st.status='1' ";
        $order = "";
        if(!empty($params['search_key'])) {

            $search_key = $params['search_key'];
          
            $where.= " WHERE title_".$lang." LIKE '%$search_key%'";
        }

        if(!empty($params['open_status'])) {
            
            
            $open_status = $params['open_status'];
            $time = date("H:i:s");
            ($open_status==1)?$where .= " AND st.open <='$time' AND st.close >= '$time'": $where .= " AND ('$time' < st.open  OR '$time' > st.close )";


        }

        if(!empty($params['distance'])) {

            $distance = $params['distance'];
            $distance==1 ?$order.=" ORDER BY distance ASC ":$order.=" ORDER BY distance DESC ";
        }


        $data = $this->callSql("SELECT st.*,( 3959 * acos( cos( radians('$lat') ) * cos( radians(st.`lat`) ) * cos(radians(st.`longitude`) - radians('$long') ) + sin( radians('$lat') ) * sin( radians( lat ) ) ) ) AS distance FROM $this->tableName st $where $order","rows");

        
        if(empty($data)) {
            
            $response['storeList'] = []; 
            return $response;
        }

        $response = [];
        if (!empty($data)) {
                foreach ($data as $key => $info) {

                    if($info['distance'] <= $radius) {
                        
                        $response[$key]['id'] = $info['id'];
                        $response[$key]['title'] = $info['title_'.$lang];
                        $response[$key]['address'] = $info['address_'.$lang];
                        $response[$key]['logo'] = !empty($info['logo'])?BASEURL.'bo/web/uploads/store/'.$info['logo']:BASEURL.'bo/web/uploads/store/default.jpeg';
                        $response[$key]['distance'] = !empty($info['distance']) ?  number_format($info['distance'],2).' mi':'0 mi';
                    }
                    
                    
               
                }
        }

        $result['storeList'] = $response; 

        return $result;
    }


    // public function getProductList($params)
    // {

    //      $lang     = $params['language'];
    //      $store_id = $params['store_id'];

    //      $where = "WHERE store_id='$store_id AND status=1'";
    //      $where1 ="";

    //     if(!empty($params['search_key'])) {

    //         $search_key = $params['search_key'];
          
    //         $where1= "AND title_".$lang." LIKE '%$search_key%'";
    //     }

    //     $data = $this->callSql("SELECT product_id,price FROM product_price $where","rows");

    //     if(empty($data)) {
            
    //         $response['storeList'] = []; 
    //         return $response;
    //     }

    //     $response = [];

    //     if (!empty($data)) {
    //             foreach ($data as $key => $info) {

    //                  $product_id = $info['product_id'];

    //                  $store = $this->callSql("SELECT title_".$lang.",logo FROM store WHERE id='$store_id'","row");
 
    //                  $product = $this->callSql("SELECT title_".$lang.",image,category_id,sub_category_id FROM product WHERE id='$product_id' $where1","rows");

    //           if (!empty($product)) {

    //                 foreach ($product as $k => $v) {

    //                  $cat_id = $v['category_id'];
    //                  $sub_cat_id = $v['sub_category_id'];

    //                  $category = $this->callSql("SELECT title_".$lang." FROM category WHERE id='$cat_id'","value");

    //                  $sub_category = $this->callSql("SELECT title_".$lang." FROM sub_category WHERE id='$sub_cat_id'","value");
                    
    //                 $response[$key]['product_id'] = $product_id;
    //                 $response[$key]['product_name'] = $v['title_'.$lang];
    //                 $response[$key]['product_image'] = !empty($v['image'])?BASEURL.'bo/web/uploads/product/'.$v['image']:BASEURL.'bo/web/uploads/product/default.jpeg';
    //                 $response[$key]['price'] = $info['price'];
    //                 $response[$key]['store_name'] = $store['title_'.$lang];
    //                 $response[$key]['store_image'] = !empty($store['logo'])?BASEURL.'bo/web/uploads/store/'.$store['logo']:BASEURL.'bo/web/uploads/store/default.jpeg';
    //                 $response[$key]['category_name'] = $category;
    //                 $response[$key]['sub_category_name'] = $sub_category;

    //                 }
               
    //             }
    //         }
    //     }

    //     $result['productDetail'] = $response; 

    //     return $result;
    // }



    public function getProductList($params)
    {

         $lang     = $params['language'];
         $where    = " WHERE product_price.status='1' ";
         $join     = '';

         if(!empty($params['store_id'])) {

            $store_id = $params['store_id'];
            $where .= " AND product_price.store_id='$store_id' ";

         }


        if(!empty($params['search_key'])) {

            $search_key = $params['search_key'];
            $where1 = " product.title_".$lang." LIKE '%$search_key%'";
            $join   = " JOIN product_price ON product.id=product_price.product_id";
            $where2 = " AND product_price.store_id='$store_id'";
            $product_ids = $this->callSql("SELECT GROUP_CONCAT(DISTINCT(product.id)) as product_ids FROM product $join WHERE $where1 $where2","value");

           
            if(empty($product_ids)) {
                
                $response['productList'] = []; 
                return $response;
            }
            $where .= " AND product_price.product_id IN($product_ids)";    
            
        }
        
        $data = $this->callSql("SELECT product_price.product_id,product_price.price,product_price.store_id FROM product_price $where","rows");


        if(empty($data)) {
            
            $response['productList'] = []; 
            return $response;
        }

        $response = [];

        if(!empty($data)) {

            foreach ($data as $key => $info) {

                $product_id = $info['product_id'];    
                $store = $this->callSql("SELECT title_".$lang.",logo FROM store WHERE id='$store_id'","row");


                $product = $this->callSql("SELECT title_".$lang.",image,category_id,sub_category_id FROM product WHERE id='$product_id'","row");

                
                $cat_id = $product['category_id'];
                $sub_cat_id = $product['sub_category_id'];
                $category = $this->callSql("SELECT title_".$lang." FROM category WHERE id='$cat_id'","value");
                $sub_category = $this->callSql("SELECT title_".$lang." FROM sub_category WHERE id='$sub_cat_id'","value");

                $response[$key]['product_id'] = $product_id;
                $response[$key]['product_name'] = $product['title_'.$lang];
                $response[$key]['product_image'] = !empty($product['image'])?BASEURL.'bo/web/uploads/product/'.$product['image']:BASEURL.'bo/web/uploads/product/default.jpeg';
                $response[$key]['price'] = !empty($info['price']) ? '$'.number_format($info['price'],2):'$0.00';
                $response[$key]['store_id'] = $store_id;
                    $response[$key]['store_name'] = $store['title_'.$lang];
                $response[$key]['store_image'] = !empty($store['logo'])?BASEURL.'bo/web/uploads/store/'.$store['logo']:BASEURL.'bo/web/uploads/store/default.jpeg';    
                $response[$key]['category_name'] = $category;
                $response[$key]['sub_category_name'] = $sub_category;

            }

            

        }

        $result['productList'] = $response; 
        return $result;


    }


    public function getProduct($params)
    {

         $lang     = $params['language'];
         $where    = " WHERE product_price.status='1'";
         $jeoin     = '';
         $whre1   = '';
         $response = [];
         $dis      = '';
         $distance_query = "";
         $pri  = "";
         $distance  = "";
         $time_status ="";

         

            $lat = $params['lat'];
            $long = $params['long'];
            $category_id = !empty($params['category']) ? $params['category'] :'';
            $sub_category_id = !empty($params['sub_category']) ? $params['sub_category'] :'';
            $product_name = !empty($params['product_name']) ? $params['product_name'] :'';
            $from  ="store st";

            //$where1 = " WHERE product.title_".$lang."='$product_name' AND product.category_id='$category_id' AND product.sub_category_id='$sub_category_id' AND status=1 ";
            if(!empty($product_name)) {

                $where1 = " WHERE product.title_".$lang." LIKE '%$product_name%' AND status='1'";

            }

            if(!empty($category_id) && !empty($sub_category_id)) {

                $where1 = " WHERE product.category_id='$category_id' AND product.sub_category_id='$sub_category_id' AND status=1 ";

            }

            $product_id = $this->callSql("SELECT GROUP_CONCAT(id) as product_id FROM product $where1","value");
                
            $where .= " AND store.lat='$lat' AND long.longitude='$long' ";
            //$where2 = " WHERE st.status=1 AND pp.`product_id`='$product_id'";
            $where2 = " WHERE st.status=1 AND pp.`product_id` IN ($product_id) AND st.status='1' AND distance<='5'";
            $order  = " ORDER BY pp.is_add ASC ";

            


            if(empty($product_id)) {

               $result['storeList'] = []; 
               $result['product_name'] = $product_name;
               
               return $result;

             }
          
            if(!empty($params['distance'])) {

                   $distance = $params['distance'];
                   $distance==1 ?$order.=" ,distance ASC ":$order.=" ,distance DESC ";
                   //$distance_query = "";
            }



            if(!empty($params['price'])) {
                
                $price = !empty($params['price']) ? $params['price'] : '';
               // $price==1 ?$pri=' pp.price ASC ':$pri=' pp.price DESC ';
                $order .= $price == '1' ? " ,pp.price ASC " : " ,pp.price DESC ";

            }


             if(!empty($params['open_status'])) {

                   $open_status = $params['open_status'];
                   $time = date("H:i:s");

                   ($open_status==1)?$where2 .= " AND st.open <='$time' AND st.close >= '$time'": $where2 .= " AND ('$time' < st.open  OR '$time' > st.close )";
            
            }


            $storeList = $this->callsql("SELECT st.`id` as store_id,st.`title_".$lang."` as store_name,st.`logo` as logo,st.`address_".$lang."` as store_address,pp.`is_add`,pp.product_id,( 3959 * acos( cos( radians('$lat') ) * cos( radians(st.`lat`) ) * cos(radians(st.`longitude`) - radians('$long') ) + sin( radians('$lat') ) * sin( radians( lat ) ) ) ) AS distance,pp.`price` from store st JOIN product_price pp ON st.id=pp.store_id ".$where2.$order,"rows");

            foreach ($storeList as $key => $value) {

                if($info['distance'] <= 5) {

                    $storeList[$key]['logo'] = !empty($value['logo']) ? BASEURL.'bo/web/uploads/store/'.$value['logo'] : BASEURL.'bo/web/uploads/store/default.png';
                    $storeList[$key]['price'] = '$'.number_format($value['price'],2);
                    $storeList[$key]['distance'] = number_format($value['distance'],2).' mi';
                    ($value['is_add']==1) ? $storeList[$key]['add_text'] =  Raise::t('common','ad'):$storeList[$key]['add_text'] ='';
                    $storeList[$key]['product_id'] = $value['product_id'];
                }
            }
 
            $result['product_name'] = !empty($product_name) ? $product_name :$this->callSql("SELECT GROUP_CONCAT(title_".$lang.") as name FROM product WHERE id IN ('$product_id')","value");
            $result['storeList'] = $storeList; 
            return $result;


    }

    public function suggestProduct($params){

        $user_id    = $params['user_id'];
        $title_en   = $params['title_en'];
        $status     = $params['status'];
        $created_at = time();
        $this->query(" INSERT INTO `product_suggetion` SET `user_id`='$user_id',`title_en`='$title_en',`status`='$status',`created_at`='$created_at'");
        return $this->execute();


    }

    public function suggestStore($params){

        $user_id    = $params['user_id'];
        $title_en   = $params['title_en'];
        $address_en = $params['address_en'];
        $status     = $params['status'];
        $created_at = time();
        $this->query(" INSERT INTO `store_suggetion` SET `user_id`='$user_id',`title_en`='$title_en',`address_en`='$address_en',`status`='$status',`created_at`='$created_at'");
        return $this->execute();


    }

    


}
