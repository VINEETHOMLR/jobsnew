<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Content extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "content";

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


    public function getList($user_id,$category_id,$sub_category_id){


       
        $response = $this->callSql("SELECT id,title,image,category_id,sub_category_id,video,pdf,status,type FROM $this->tableName WHERE  status=1 AND category_id=$category_id AND sub_category_id=$sub_category_id ORDER BY type DESC ","rows");

        $rows = array();



        if (!empty($response)) {
                foreach ($response as $key => $info) {
                    
                    $course_type  = $info['type'] ;//1-paid course 2-free course 
                    $show_buy_btn = '0';


                    $paidDetails  = (new UserPurchase)->checkUserPaid($user_id,$category_id); 

                    if(empty($paidDetails) && $course_type == '1') { //show buy button
                       
                       $show_buy_btn = '1';
                    }

                    if(!empty($paidDetails) && $course_type == '1') { //dont show buy button
                       
                       $show_buy_btn = '0';
                    }
                    
                    $rows[$key]['course_id']               = !empty($info['id'])?strval($info['id']):'-';
                    $rows[$key]['title']            = !empty($info['title'])?$info['title']:'-';
                    //$rows[$key]['image']            = !empty($info['image'])?BOUPLOADPATH.'category/'.$info['image']:'';
                    $rows[$key]['show_buy_button']  = $show_buy_btn;
                    $rows[$key]['course_type']      = $course_type == '1' ? 'Premium':'Free';

                }
        }

       

        return !empty($rows) ? $rows :$rows;
    }


    public function getDetails($id){
        
         return $this->callSql("SELECT id,title,image,video,pdf,status,category_id,type,sub_category_id FROM $this->tableName WHERE  id=$id AND status=1","row");

    }


    public function getContentFiles($content_id,$type){
        
        $list = $this->callSql("SELECT file FROM files WHERE  content_id=$content_id AND type=$type AND status=1","rows");

        $path = $type == '1' ? BOUPLOADPATH.'contentpdf/' : BOUPLOADPATH.'contentvideo/';
        foreach($list as $k=>$v) {
            
            $list[$k]['file'] = $path.$v['file'];
        }

        return $list;
    }


    
    

    

    
   
    
}
