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


    public function getCategory()
    {

       
        $response = $this->callSql("SELECT id,name,parent_category_id,status,image,icon  FROM $this->tableName  WHERE  status=1 ORDER BY id DESC ","rows");

        $category_list = array();

        if(empty($response)) {
             
             return []; 
        }

        if (!empty($response)) {

            foreach ($response as $key => $info) {
                   
                $response[$key]['icon'] = !empty($info['icon'])?BASEURL.'bo/web/upload/category/icon/'.$info['icon']:BASEURL.'bo/web/upload/category/icon/default.png';

                $response[$key]['image'] = !empty($info['image'])?BASEURL.'bo/web/upload/category/image/'.$info['image']:BASEURL.'bo/web/upload/category/image/default.png';

                
            }
        }

        $result = $response; 

        return $result;
    }  


    public function getFavCategory()
    {

       
        $response = $this->callSql("SELECT ca.id,ca.name,ca.parent_category_id,ca.status,ca.image,ca.icon,

        (SELECT COUNT(*)  FROM job_post jp  WHERE jp.category_id = ca.id ) AS job_post_count

        FROM  category AS ca

        WHERE ca.status = 1 ORDER BY   job_post_count DESC","rows");

        $category_list = array();

        if(empty($response)) {
             
             return []; 
        }

        if (!empty($response)) {

            foreach ($response as $key => $info) {
                   
                $response[$key]['icon'] = !empty($info['icon'])?BASEURL.'bo/web/upload/category/icon/'.$info['icon']:BASEURL.'web/upload/category/icon/default.png';

                $response[$key]['image'] = !empty($info['image'])?BASEURL.'bo/web/upload/category/image/'.$info['image']:BASEURL.'web/upload/category/image/default.png';

                
            }
        }

        $result = $response; 

        return $result;
    } 

    public function parentCategoryDetails($id)
    {


        $sql = "SELECT * FROM parent_category WHERE id='$id'";
        return $this->callsql($sql,'row');

    }
   
    
}
