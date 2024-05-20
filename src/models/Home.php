<?php


namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;


class Home extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "book";

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
 

   public function getTrending($search){
        
        $where = " WHERE status=1 ";
        if(!empty($search)) {
            
            $where.= " AND title LIKE '%$search%'";
        }

        $response = $this->callSql("SELECT * FROM $this->tableName $where ORDER BY count DESC, id DESC LIMIT 5","rows");

        $rows = array();
        if (!empty($response)) {
                foreach ($response as $key => $info) {
                    
                    $author_id  = $info['user_id'];
                    $author = $this->callSql("SELECT fullname FROM user WHERE id=$author_id","value");
                   
                    $rows[$key]['id']    = !empty($info['id'])?strval($info['id']):'-';
                    $rows[$key]['title']  = !empty($info['title'])?$info['title']:'-';
                    $rows[$key]['author']  = !empty($author)?$author:'-';
                    $rows[$key]['cover_photo'] = !empty($info['cover_photo'])?BASEURL.'web/uploads/cover/'.$info['cover_photo']:'';
                    $rows[$key]['synopsis']  = !empty($info['synopsis'])?$info['synopsis']:'-';
                    
                }
        }

        return !empty($rows) ? $rows :$rows;

    }  
    
}
