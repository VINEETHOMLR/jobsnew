<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Category extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "category";

        $this->columns = [
            'id', 
            'title', 
            'message',
            'createtime',  
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
            'message', 
            'createtime', 
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


    public function getList($lang){
 
        $title = 'title_'.$lang;
       
        $response = $this->callSql("SELECT id,$title FROM $this->tableName WHERE  status=1 ORDER BY id DESC ","rows");

        $category_list = array();

        if(empty($response)) {
             
             return []; 
        }

        if (!empty($response)) {

                foreach ($response as $key => $info) {

                    $cat_id = $info['id'];
                    $category_list[$key]['category_id']   = $cat_id;
                    $category_list[$key]['category_name'] = $info[$title];

                    $result = $this->callSql("SELECT id,$title FROM  sub_category WHERE category_id='$cat_id' AND status=1 ORDER BY id DESC ","rows");
                 
                    if (!empty($result)) { 

                           foreach ($result as $k => $val) {  
                            // print_r($val);exit;

                                $category_list[$key]['subcategory_list'][$k]['subcategory_id']  = $val['id'];
                                $category_list[$key]['subcategory_list'][$k]['subcategory_name']  = $val[$title];     
                          }

                   } else {
                               $category_list[$key]['subcategory_list'] = [];
                   }

        } 

        return !empty($category_list) ? $category_list :$category_list;
    }

}


    public function getSubcategoryList($category_id){

        $response = $this->callSql("SELECT id,name,category_id FROM sub_category WHERE  category_id=$category_id AND status=1 ORDER BY id DESC ","rows");

        $rows = array();

        if (!empty($response)) {
                foreach ($response as $key => $info) {
                    

                    $rows[$key]['subcategory_id']    = !empty($info['id'])?strval($info['id']):'0';
                    $rows[$key]['name']  = !empty($info['name'])?$info['name']:'-';
                    $rows[$key]['category_id']  = !empty($info['category_id'])?$info['category_id']:'0';
                    
                }
        }

       

        

        return !empty($rows) ? $rows :$rows;    
    }

    public function getDetails($id){
        
        return $this->callSql("SELECT id,title,sub_title,image,status,price,duration FROM $this->tableName WHERE  status=1 AND id=$id","row");
    }

    public function getSubcategoryDetails($id){
        
        return $this->callSql("SELECT *  FROM sub_category WHERE  status=1 AND id=$id","row");
    }
    
    

    

    
   
    
}
