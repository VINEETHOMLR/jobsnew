<?php
namespace src\lib;
use inc\Raise;
    
class MessageVariable {
    protected $validFile = false;
    protected $sheet = '';
    protected $headers = [];
    protected $userColName = 'recipient';
    protected $userColIdx = '';
    protected $userCol = '';
    protected $player_user_map = [];

    public function __construct(){
        $dir = __DIR__ . '/test.csv';
        $file_exists = file_exists($dir);
        
        // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        // $reader->setInputEncoding('GBK');
        // $reader->setDelimiter(';');
        // $reader->setEnclosure('');
        // $reader->setSheetIndex(0);
        // $spreadsheet = $reader->load($dir);
        
        if($file_exists === true && pathinfo($dir)['extension'] == 'csv'){
            $this->sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($dir)->getActiveSheet();
            
            // $this->sheet = $spreadsheet->getActiveSheet();
            $this->highCol = $this->sheet->getHighestColumn();
            $this->highRow = $this->sheet->getHighestRow();
            $this->initHeaders();

            $player_user = getModel('PlayerUser');
            $player_user->query("SELECT id, username FROM player_user");
            $player_user->execute();
            $player_users = $player_user->resultset();
            $this->player_user_map = array_merge(...array_map(function($au){
                return [$au['username'] => $au['id']];
            }, $player_users));
            $this->validFile = true;
        }
    }


    public function isValidFile(){
        return $this->validFile == true;
    }

    public function initHeaders(){
        $rowIterator = $this->sheet->getRowIterator();
        $arr = [];
        foreach($rowIterator as $row){
            if(sizeof($arr) > 0){
                continue;
            }
            $cellIterator = $row->getCellIterator();
            foreach($cellIterator as $cell){
                if($cell->getValue() == $this->userColName){
                    $this->userCol = $cell->getColumn();
                }
                $arr[$cell->getColumn()] = $cell->getValue();
            }
        }
        $this->headers = $arr;
    }

    public function getSheet(){
        return $this->sheet;
    }

    public function isValidSheet(){
        return $this->sheet instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
    }

    public function getHeaders(){
        return $this->headers;
    }

    public function hasHeader($header){
        return array_intersect($header, array_values($this->headers)) == $header;
    }

    public function getColumnValues($column){
        $colIdx = array_search($column, $this->headers);
        $alpha = range('A', 'Z');
        $col = $alpha[$colIdx];
        $highRow = $this->highRow;
        $colVals = array_merge(...$this->sheet->rangeToArray("{$col}2:{$col}{$highRow}"));
        return $colVals;
    }

    public function getRowValues(){
        $map = $this->player_user_map;
        $key = $this->headers[$this->userCol];
        
        $rowIterator = $this->sheet->getRowIterator();
        $arr = [];
        foreach($rowIterator as $row){
            $tmp = [];
            $player = '';
            $cellIterator = $row->getCellIterator();
            foreach($cellIterator as $cell){
                
                if($this->headers[$cell->getColumn()] == $this->headers[$this->userCol]){
                    $player = $cell->getValue();
                }
                $tmp[$this->headers[$cell->getColumn()]] = $cell->getValue();
            }


            if(isset($map[$player])){
                $arr[$map[$player]] = $tmp;
            }else{
                $arr[$player] = $tmp;
            }
        }
        
        unset($arr[$this->userColName]);
        return $arr;
    }

    public function storeVariables($msg_id, $players){

        $rowValues = $this->getRowValues();
        $rowValues = array_filter($rowValues, function($v, $k)use($players){
            return in_array($k, $players);
        }, ARRAY_FILTER_USE_BOTH);
        
        
        $pass = true;
        foreach($rowValues as $player => $row){
            $tmp = [];
            $tmp['var'] = json_encode($row, JSON_UNESCAPED_UNICODE);
            $tmp['player_id'] = $player;
            $tmp['message_id'] = $msg_id;
            $isCreated = getModel('MessageVariable')->createRecord($tmp);
            if(!is_numeric($isCreated)){
                $pass = false;
            }
        }
        return $pass === true ? $rowValues : false;
    }
    
    public function validateUser(){
        // $rv = (new \src\lib\MessageVariable)->getRowValues();
        // $users = array_keys($rv);
        // $admin_user = getModel('AdminUser');
        // $admin_user->query("SELECT admin_user_id, admin_user_name FROM admin_user");
        // $admin_user->execute();
        // $admin_users = $admin_user->resultset();
        // $admin_user_map = array_merge(...array_map(function($au){
        //     return [$au['admin_user_name'] => $au['admin_user_id']];
        // }, $admin_users));
        
        // array_intersect_key($admin_user_map, array_flip($users));
    }
}