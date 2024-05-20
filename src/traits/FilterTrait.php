<?php

namespace src\traits;

trait FilterTrait
{
    protected $bindingArr = [];
    protected $prms = [];
    protected $whr = [];

    /**
     * helper for buildFilterByCol - add value to bindingArr
     *
     * @param mixed $value
     * @property-write array $this->bindingArr
     * @return string $parameter for binding
     */
    public function getBind($value)
    {

        $this->bindingArr[] = $value;

        return '?';
    }

    /**
     * helper for buildFilterByCol - add value to bindingArr
     *
     * @param array $value
     * @property-write array $this->bindingArr
     * @return string $parameter for binding
     */
    public function getBindIn($valueArr)
    {

        foreach ($valueArr as $id) {
            $this->bindingArr[] = $id;
        }

        return str_repeat('?,', count($valueArr) - 1) . '?';
    }

    /**
     * Helper to add filter into query & binding
     *
     * @param string $colName
     * @param array $option
     * @property-write array $this->whr
     * @return void
     */
    public function buildFilterByCol($colName, $option = [])
    {

        $table = issetGet($option,'table',$this->tableName);
        $action = issetGet($option,'action','=');
        $paramName = issetGet($option,'pName',$colName);

        $query = $table . "." . $colName;

        if (issetNotEmpty($this->prms, $paramName) || isset($option['overrideVal'])) {
            // in expecting array
            if (!isset($option['overrideVal']) && gettype($this->prms[$paramName]) == 'string' && $action == 'IN') {
                $this->prms[$paramName] = explode(",", $this->prms[$paramName]);
            }

            $colNameVal = issetGet($option,'overrideVal',$this->prms[$paramName]);

            switch ($action) {

                case 'IN':$stm = $this->getBindIn($colNameVal);
                    $query .= "  in (" . $stm . ")";
                    break;

                default:$stm = $this->getBind($colNameVal);
                    $query .= $action . $stm;
                    break;

            }

            array_push($this->whr, $query);

        }

    }

}