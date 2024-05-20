<?php

namespace src\lib;

/**
 * Simple Excel export
 *
 * 
 */
class Excel {

    /**
     *
     * @var String 
     */
    public $filename = '';

    /**
     *
     * @var String 
     */
    public $type = 'excel';

    public function __construct() {
        $this->filename = "website_data_" . time();
    }

    /**
     * 
     * @param String $str
     */
    public static function cleanExcelData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    /**
     * 
     * @param String $str
     */
    function cleanCsvData(&$str) {
        if ($str == 't')
            $str = 'TRUE';
        if ($str == 'f')
            $str = 'FALSE';
        if (preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
            $str = "'$str";
        }
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    /**
     * Set headers and encoding for Excel File
     */
    private function setExcelHeaders() {
        header("Content-Disposition: attachment; filename=\"$this->filename" . '.xls' . "\"");
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    /**
     * Set headers and encoding for CSV File
     */
    private function setCsvHeaders() {
        header("Content-Disposition: attachment; filename=\"$this->filename" . '.csv' . "\"");
        header("Content-Type: text/csv; charset=UTF-8");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    /**
     *  $data = [
      ["firstname" => "Mary", "lastname" => "Johnson", "age" => 25],
      ["firstname" => "Amanda", "lastname" => "Miller", "age" => 18],
      ["firstname" => "James", "lastname" => "Brown", "age" => 31]
     * ];
     * @param Array $data
     */
    public function process($data) {
        if ($this->type === 'excel') {
            $this->saveExcel($data);
        } elseif ($this->type === 'csv') {
            $this->saveCSV($data);
        }
    }

    /**
     * 
     * @param Array $data
     */
    protected function saveCSV($data) {
        $this->setCsvHeaders();
        $out = fopen("php://output", 'w');
        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                fputcsv($out, array_keys($row), ',', '"');
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\Excel::cleanCsvData');
            fputcsv($out, array_values($row), ',', '"');
        }
        fclose($out);
        exit;
    }

    /**
     * 
     * @param Array $data
     */
    protected function saveExcel($data) {
        $this->setExcelHeaders();
        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                mb_convert_encoding(implode("\t", array_keys($row)) . "\n", 'utf-16', 'utf-8');
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\Excel::cleanExcelData');
            echo mb_convert_encoding(implode("\t", array_values($row)) . "\n", 'utf-16', 'utf-8');
        }
        exit;
    }

}
