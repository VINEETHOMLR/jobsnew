<?php

namespace src\lib;

define('WITHSCORES', true);

/**
 * 
 * Redis Wrapper Class
 */
class RRedis {

    const TIME_SHORT = 60;
    const TIME_MEDIUM = 120;
    const TIME_LONG = 300;

    /**
     *
     * @var Boolean
     */
    private $connStatus = false;

    /**
     *
     * @var Mixed
     */
    private $instance = false;

    /**
     *
     * @param String $db
     * @return boolean
     */
    public function __construct($db = null) {
        /*try {
            if (ENV === "dev") {
                $redis = new \Redis();
                $redis->connect(REDIS_CONNECTION);
                if (defined('REDIS_AUTH')) {
                    $redis->auth(REDIS_AUTH);
                }
                $this->instance = $redis;
                $this->instance->select($db === null ? REDIS_DB : $db);
            } else {
                $this->instance = new \RedisCluster(null, [REDIS_CONNECTION . ':6379'], 1, 1, true); //connections,timeout, read_timeout, persistent
            }

            $this->connStatus = true;
        } catch (\RedisClusterException $e) {
            error_log($e->getMessage());
            $this->connStatus = false;
        }*/
    }

    /**
     *
     * @param String $method
     * @param Mixed $arguments
     * @return Function Call
     */
    public function __call($method, $arguments) {
       /* if (method_exists($this, $method) && $this->connStatus === true) {
            return call_user_func_array([$this, $method], $arguments);
        } else {
            return false;
        }*/
    }

    /**
     *
     * @param String $key
     * @param Mixed | Array | String $val
     * @param Integer $timeout in Seconds
     * @return type
     */
    public function set($key, $val, $timeout = null, $opt = null) {
        /*if ($this->connStatus === true) {
            $value = (is_array($val)) ? json_encode($val) : $val;
            if ($timeout === null) {
                $timeout = self::TIME_SHORT;
            }
            return $this->instance->set($key, $value, ['nx', 'ex' => $timeout]);
        }
        return false;*/
    }

    /**
     *
     * @param String $key
     * @return String
     */
    public function get($key) {
        /*if ($this->connStatus === true) {
            return $this->instance->get($key);
        }
        return false;*/
    }

    /**
     *
     * @param String $key
     * @return Boolean
     */
    public function exists($key, ...$other_keys) {
        /*if ($this->connStatus === true) {
            return $this->instance->exists($key, ...$other_keys);
        }
        return false;*/
    }

    /**
     *
     * @param String $key
     * @return Boolean
     */
    public function del($key, ...$other_keys) {
        /*if ($this->connStatus === true) {
            return $this->instance->del($key, ...$other_keys);
        }
        return false;*/
    }

    // List related
    /**
     * righ push to list
     *
     * @param string $key
     * @param string ...$val
     * @return void
     */
    public function rPush($key, ...$val)
    {
        /*if ($this->connStatus === true) {
            return $this->instance->rPush($key, ...$val);
        }
        return false;*/

    }

    /**
     * get list by index
     *
     * @param string $key
     * @param int $index
     * @return mixed
     */
    public function lindex($key, $index)
    {
        /*if ($this->connStatus === true) {
            return $this->instance->lindex($key, $index);
        }
        return false;*/

    }

    /**
     * left pop item from list
     *
     * @param string $key
     * @return mixed
     */
    public function lPop($key)
    {
        /*if ($this->connStatus === true) {
            return $this->instance->lPop($key);
        }
        return false;*/

    }
    /**
     * get list by range
     *
     * @param string $key
     * @param int $start
     * @param int $end
     * @return mixed
     */
    public function lRange($key, $start, $end)
    {
       /* if ($this->connStatus === true) {
            return $this->instance->lRange($key, $start, $end);
        }
        return false;*/

    }

    /**
     * remove first $count occurrences of the value in list
     * If $count is zero, all the matching elements are removed. If count is negative, elements are removed from tail to head.
     *
     * @param string $key
     * @param string $value
     * @param int $count
     * @return mixed
     */
    public function lRem($key, $value, $count)
    {
       /* if ($this->connStatus === true) {
            return $this->instance->lRem($key, $value, $count);
        }
        return false;*/

    }

    /**
     * Returns the time to live left for a given key in seconds
     *
     * @param string $key
     * @return seconds
     */
    public function ttl($key)
    {
        /*if ($this->connStatus === true) {
            return $this->instance->ttl($key);
        }
        return false;*/

    }

    public function checkConn() {

         //return $this->connStatus;
    }

    /**
     * Close the connection when class closure
     */
    public function __destruct() {
        /*if (is_object($this->instance)) {
            $this->instance->close();
        }*/
    }

}
