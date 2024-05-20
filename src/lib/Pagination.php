<?php

namespace src\lib;

/**
 * Customized Pagination Handler File
 *
 * 
 */
class Pagination {

    /**
     *
     * @var Object Model
     */
    private $model;

    /**
     *
     * @var INT
     */
    public $totRecords = 0;

    /**
     *
     * @var Array
     */
    public $filters = [];

    /**
     *
     * @var INT 
     */
    public $page = 1;

    /**
     *
     * @var INT 
     */
    public $start = 1;

    /**
     *
     * @var INT 
     */
    public $end = 0;

    /**
     *
     * @var INT 
     */
    public $limit = 25;

    /**
     *
     * @var String 
     */
    public $sort = '';
    
    /**
     *
     * @var INT
     */
    public $lastPK = 0;

    public function __construct($model, $filters, $page = 1) {
        $this->model = $model;
        $this->filters = $filters;
        $this->page = $page;
        $this->setCount();
    }

    /**
     * Method to calculate the total records
     * @return NULL
     */
    public function setCount() {
        $where = $this->model->where($this->filters);
        $this->model->query("SELECT count(1) as totCnt FROM {$this->model->tableName} $where");
        $this->model->attrBind($this->filters);
        $this->totRecords = $this->model->single()['totCnt'];
    }

    /**
     * 
     * @return Array
     */
    public function getData() {
        $offset = (isset($this->page) && (int) $this->page >= 1) ? $this->limit * ($this->page - 1) : 0;
        return $this->model->findAll($this->filters, $this->sort, ' LIMIT ' . $offset . ', ' . $this->limit);
    }

    /**
     * Method to return the stripped alphanumeric
     * @param String $attr
     * @return String
     */
    private function stripAttr($attr) {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $attr);
    }

    /**
     * Method to do pagination with speed query optimization
     * @return Array
     */
    public function getFastData() {
        $pk = $this->model->getPK();
        $where = $this->model->where($this->filters);
        $pkWhere = "`{$pk}` > :pk{$this->stripAttr($pk)}";
        if ((int) $this->lastPK > 0) {
            $where .= !empty($where) ? " AND {$pkWhere}" : " WHERE " . $pkWhere;
        }
        try {
            $this->model->query("SELECT * FROM `{$this->model->tableName}` {$where} ORDER BY `{$pk}` ASC LIMIT {$this->limit}");
            $this->model->attrBind($this->getWherePK($pk));
            $res = $this->model->resultset();
        } catch (\PDOException $pdoE) {
            echo '<pre>';
            print_r($pdoE);
            echo '</pre>';
            die;
        }
        $this->setLastPK($res, $pk);
        return $res;
    }

    /**
     * 
     * @param String $pk
     * @return Array
     */
    private function getWherePK($pk) {
        $filter = $this->filters;
        if ((int) $this->lastPK > 0) {
            $filter["pk{$this->stripAttr($pk)}"] = (string) $this->lastPK;
        }
        return $filter;
    }

    /**
     * 
     * @param Array $res
     * @param String $pk
     */
    public function setLastPK($res, $pk) {
        $last = end($res);
        $this->lastPK = array_key_exists($pk, $last) ? $last[$pk] : 0;
    }

}
