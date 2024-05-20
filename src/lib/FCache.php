<?php

namespace src\lib;

/**
 * For managing the File Cache
 */
class FCache {

    /**
     *
     * @var String 
     */
    private $cacheDir = '';

    /**
     * 
     * @param String $db
     * @return boolean
     */
    public function __construct() {
        $this->cacheDir = BASEPATH . '/web/cache';
        $this->newDir($this->cacheDir);
    }

    /**
     * 
     * @param String $dir
     */
    private function newDir($dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * 
     * @param String $fileName
     * @param Mixed $content
     * @return Boolean
     */
    public function set($fileName, $content) {
        $fullName = $this->cacheDir . '/' . $fileName;
        $data = is_array($content) ? json_encode($content) : $content;
        return file_put_contents($fullName . '.data', $data, LOCK_EX);
    }

    /**
     * 
     * @param String $fileName
     * @param Mixed $content
     * @return Boolean
     */
    public function get($fileName) {
        $fullName = $this->cacheDir . '/' . $fileName;
        return file_get_contents($fullName . '.data');
    }

    /**
     * 
     * @param String $fileName
     * @return Boolean
     */
    public function exists($fileName) {
        return file_exists($this->cacheDir . '/' . $fileName . '.data');
    }

    /**
     * 
     * @param String $fileName
     * @return Boolean
     */
    public function del($fileName) {
        if ($this->exists($fileName)) {
            return unlink($this->cacheDir . '/' . $fileName . '.data');
        }
    }

}

?>