<?php

namespace src\lib;

/**
 * Core Database Resource Handler File
 *
 * 
 */
class Database
{

    /**
     *
     * @var String
     */
    protected $pk = '';

    protected $dbh;
    protected $error;
    protected $stmt;
    public $tableName = '';

    /**
     *
     * @param String $db
     */
    public function __construct($db)
    {
        //this variable @ root index.php
        global $rootConnection;

        if (isset($rootConnection[$db['host'] . $db['dbname']])) {
            $this->dbh = $rootConnection[$db['host'] . $db['dbname']];
            return;
        }

        // Set DSN
        $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=utf8';
        // Set options
        $options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        );
        // Create a new \PDO instanace
        try {
            $this->dbh = new \PDO($dsn, $db['user'], $db['pass'], $options);
            $rootConnection[$db['host'] . $db['dbname']] = $this->dbh;

        }
        // Catch any errors
         catch (\PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     *
     * @param String $query
     */
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    /**
     *
     * @param Array $param
     * @param String $value
     * @param Mixed $type
     */
    public function bind($param, $value, $type = null)
    {

        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_STR;
                    $value = '';
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute($para = null)
    {
        return $this->stmt->execute($para);
    }

    public function resultset($para = null)
    {

        $this->execute($para);
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function single($para = null)
    {

        $this->execute($para);
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function getError()
    {
        return $this->error;
    }

    public function getValue($para = null)
    {
        $row = $this->single($para);
        if (!empty($row)) {
            return current($row);
        }
        return '';
    }

    public function getConnection()
    {
        return $this->dbh;
    }

    /**
     *
     * @param String $query
     * @param String $type
     * @return Mixed
     */
    public function callSql($query, $type = 'row')
    {
        $row = [];
        $this->query($query);
        if (!empty($type)) {
            $row = ($type == 'rows') ? $this->resultSet() : (($type == 'row' || $type == 'value') ? $this->single() : []);
        } else {
            $this->execute();
        }

        if ($type == 'value') {
            if (!empty($row)) {
                return current($row);
            }
            return '';
        } else if ($type == 'row' or $type == 'rows') {
            return $row;
        }
    }

    /**
     * Method to return the stripped alphanumeric
     * @param String $attr
     * @return String
     */
    private function stripAttr($attr)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $attr);
    }

    /**
     *
     * @param Array $attrs
     * @return String
     */
    public function where($attrs = [])
    {
        $where = '';
        if (!empty($attrs)) {
            $cnt = 1;
            $where = ' WHERE ';
            foreach ($attrs as $attr => $val) {
                $or = (is_array($val) && array_key_exists('||', $val)) ? ' OR ' : ' AND ';
                $and = $cnt !== 1 ? $or : ' ';
                $where .= (is_array($val) && array_key_exists('||', $val)) ? $and . $attr . " = :" . $this->stripAttr($attr) : (is_array($val) ? $and . $attr . " IN ( :id_" . implode(',:id_', array_keys($val)) . ")" : $and . $attr . " = :" . $this->stripAttr($attr));
                $cnt++;
            }
        }
        return $where;
    }

    /**
     *
     * @param Array $attrs
     */
    public function attrBind($attrs = [])
    {
        if (!empty($attrs)) {
            foreach ($attrs as $attr => $val) {
                if (is_array($val)) {
                    if (array_key_exists('||', $val)) {
                        $this->bind(':' . $this->stripAttr($attr), $val['||']);
                    } else {
                        foreach ($val as $k => $v) {
                            $this->bind(':id_' . $k, $v);
                        }
                        if (count($val) === 0) {
                            $this->bind(':id_', '');
                        }
                    }
                } else {
                    $this->bind(':' . $this->stripAttr($attr), $val);
                }
            }
        }
    }

    /**
     *
     * @param Array $attrs
     * @return Array
     */
    public function findAll($attrs = [], $orderBy = '', $limit = '')
    {
        $where = $this->where($attrs);
        $order = ($orderBy !== '') ? ' ORDER BY ' . $orderBy : '';
        $query = "SELECT * FROM $this->tableName " . $where . ' ' . $order . ' ' . $limit;
        $this->query($query);
        $this->attrBind($attrs);
        return $this->resultset();
    }

    /**
     *
     * @return String
     */
    public function getPK()
    {
        //if model class defined pk , no need to query db
        if (isset($this->pk)) {
            return $this->pk;
        }

        $query = "SHOW KEYS FROM $this->tableName WHERE Key_name = 'PRIMARY'";
        $this->query($query);
        $dt = $this->single();
        return isset($dt['Column_name']) ? $dt['Column_name'] : '';
    }

    /**
     *
     * @param INT $pk
     * @return Array
     */
    public function findByPK($pk)
    {
        $pkColumn = ($this->pk && $this->pk !== '') ? $this->pk : $this->getPK();
        $query = "SELECT * FROM $this->tableName WHERE " . $pkColumn . "='$pk'";
        $this->query($query);
        $single = $this->single();
        return isset($single) && is_array($single) ? $single : [];
    }

    /**
     *
     * @param String $pk
     * @return Boolean
     */
    public function deleteByPK($pk)
    {
        $query = "DELETE FROM $this->tableName WHERE " . $this->getPK() . "='$pk'";
        $this->query($query);
        return $this->execute();
    }

    /**
     *
     * @param Mixed $value
     * @return Mixed
     */
    public function getProcType(&$value)
    {
        switch (true) {
            case is_int($value):
                return \PDO::PARAM_INT;
            case is_bool($value):
                return \PDO::PARAM_BOOL;
            case is_null($value):
                $type = \PDO::PARAM_STR;
                $value = '';
                return $type;
            default:
                return \PDO::PARAM_STR;
        }
    }

    /**
     *
     * @param String $type
     * @param Array $params
     * @return String
     */
    public function genExp($type = '', $params = [], $isSelect = false)
    {
        if (count($params) === 0) {
            return '';
        }
        if ($isSelect === true) {
            $process = [];
            foreach ($params as $param => $key) {
                $process[] = $type . $param . ' as ' . $param;
            }
            return implode(',', $process);
        } else {
            return $type . implode(',' . $type, array_keys($params));
        }
    }

    /**
     *
     * @param String $proc
     * @param Array $inParams
     * @param Array $outParams
     * @return Mixed
     */
    public function callProc($proc = '', $inParams = [], $outParams = [])
    {
        $comma = count($inParams) > 0 && count($outParams) > 0 ? ',' : '';
        $sql = 'CALL ' . $proc . '(' . $this->genExp(':', $inParams) . $comma . $this->genExp('@', $outParams) . ')';
        $this->query($sql);
        foreach ($inParams as $param => $val) {
            $type = $this->getProcType($val);
            $this->stmt->bindParam(':' . $param, $val, $type);
        }
        $this->stmt->execute();
        $this->stmt->closeCursor();
        $this->query("SELECT " . $this->genExp('@', $outParams, true));
        return $this->single();
    }

    /**
     *
     * @return Array | Mixed | Null
     */
    private function getColumns()
    {
        $attrs = [];
        //no need to query every single time, model class should have column property (attrs)
        //$this->query('SHOW COLUMNS FROM ' . $this->tableName);

        //this function move from foreach statement - one time enough
        $pk = (string) $this->getPK();
        //here redirect to model attrs (child)
        foreach ($this->attrs() as $rec) {
            if ((string) $rec !== $pk) {
                $attrs[$rec] = isset($this->{$rec}) ? $this->{$rec} : '';
            }
        }
        return $attrs;
    }

    /**
     *
     * @return Mixed | Null | Integer
     *
     */
    public function save()
    {
        try {
            $attr = $this->getColumns();
            $this->query('INSERT INTO ' . $this->tableName . '(`' . implode('`,`', array_keys($attr)) . '`) VALUES(:' . implode(',:', array_keys($attr)) . ')');
            foreach ($attr as $param => $value) {
                $value = $this->autoAssignDefaultAttr($param, $value);
                $this->bind($param, $value);
            }
            $this->execute();
            return $this->lastInsertId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @param String $attrName
     * @param String $value
     * @param String $funcName
     * @return String
     */
    private function autoAssignDefaultAttr($attrName, $value, $funcName = 'save')
    {
        if ($value != '') {
            return $value;
        }
        if ($funcName == 'save') {
            switch ($attrName) {
                case 'created_at':$value = time();
                    break;
                case 'created_by':$value = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';
                    break;
                case 'created_ip':$value = getClientIP();
                    break;
                case 'updated_ip':$value = getClientIP();
                    break;
                default:$value = '';
            }
        } elseif ($funcName == 'update') {
            switch ($attrName) {

                case 'updated_at':$value = time();
                    break;
                case 'updated_by':$value = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : '';
                    break;
                case 'updated_ip':$value = getClientIP();
                    break;
                default:$value = '';
            }
        }
        return $value;
    }

    /**
     *
     * @return Array
     */
    private function getValAttrs()
    {
        $attrs = [];
        foreach ($this->attrs() as $attr) {
            if (isset($this->{$attr}) && (!empty($this->{$attr}) || $this->{$attr} == 0)) {
                $attrs[$attr] = $this->{$attr};
            }
        }
        //append field to attrs array if matchted
        if (method_exists($this, 'attrs')) {
            $autoAssignField = ['updated_at', 'updated_by', 'updated_ip'];
            $matchAttrs = array_intersect_key(array_flip($this->attrs()), array_flip($autoAssignField));
            $matchAttrs = array_map(function () {return '';}, $matchAttrs);
            $attrs = array_merge($attrs, $matchAttrs);

        }
        return $attrs;
    }

    /**
     *
     * @return Mixed | Null | Integer
     */
    public function update($where = [])
    {
        $attr = $this->getValAttrs();
        $setAttrs = [];
        $whereAttr = '';
        foreach ($attr as $k => $v) {
            $setAttrs[] = $k . ' = :' . $k;
        }
        if (!array_key_exists($this->getPK(), $where)) {
            $where[$this->getPK()] = $this->{$this->getPK()};
        }
        $whereAttr = $this->where($where);
        $this->query('UPDATE ' . $this->tableName . ' SET ' . implode(',', $setAttrs) . ' ' . $whereAttr);
        foreach ($attr + $where as $param => $value) {
            $value = $this->autoAssignDefaultAttr($param, $value, __FUNCTION__);
            $this->bind($param, $value);
        }
        return $this->execute();
    }

    /**
     * convertTimeFormat - take column arr and convert column value to timestamp
     * if columnArr empty then will check is model has <dateColumn>  property
     * example refer to models/PlayerGroup
     *
     * @param array $columnArr
     * @return void
     */
    public function convertColToTime($columnArr = [])
    {
        if (empty($columnArr) && isset($this->dateColumn) && !empty($this->dateColumn)) {

            $columnArr = $this->dateColumn;
        }

        foreach ($columnArr as $column) {
            if ($this->{$column} != '0' && $timeStamp = strtotime($this->{$column})) {
                $this->{$column} = $timeStamp;
            }

        }

    }

    /**
     * convertColToDate
     * if columnArr empty then will check is model has <dateColumn> property
     *
     * @param array $columnArr
     * @param string $dateFormat
     * @return void
     */
    public function convertColToDate($columnArr = [], $dateFormat = "Y-m-d")
    {
        if (empty($columnArr) && isset($this->dateColumn) && !empty($this->dateColumn)) {
            $columnArr = $this->dateColumn;
        }

        foreach ($columnArr as $column) {
            if ($this->{$column} != '0' && is_numeric($this->{$column})) {
                $this->{$column} = date($dateFormat, $this->{$column});
            }

        }
    }

}

/**
 *
 */
trait Validation
{

    /**
     *
     * @var Array
     */
    private $valids = [
        'bool' => FILTER_VALIDATE_BOOLEAN,
        'email' => FILTER_VALIDATE_EMAIL,
        'float' => FILTER_VALIDATE_FLOAT,
        'int' => FILTER_VALIDATE_INT,
        'ip' => FILTER_VALIDATE_IP,
        'url' => FILTER_VALIDATE_URL,
        'encode' => FILTER_SANITIZE_ENCODED,
        'quote' => FILTER_SANITIZE_MAGIC_QUOTES,
        'spl_char' => FILTER_SANITIZE_SPECIAL_CHARS,
        'str' => FILTER_SANITIZE_STRING,
        'url' => FILTER_SANITIZE_URL,
    ];

    /**
     *
     * @var Array
     */
    private $errors = [];

    /**
     * Validate the input fields
     */
    public function validate()
    {
        foreach ($this->rules() as $key => $attrs) {
            $allAttrs = (is_string($attrs)) ? [$attrs] : $attrs;
            foreach ($allAttrs as $attr) {
                $val = $this->$attr;
                if (!filter_var($val, $this->valids[$key])) {
                    $this->errors[$attr] = 'Invalid ';
                }
            }
        }
        return count($this->errors) === 0;
    }

    /**
     *
     * @return Boolean
     */
    public function getErrors()
    {
        return $this->errors;
    }

}