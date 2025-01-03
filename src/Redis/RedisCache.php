<?php

namespace Faj1\Utils\Redis;

use Exception;
use Redis;

class RedisCache {
    private $redis;
    private $host;
    private $port;

    public function __construct($host = '127.0.0.1', $port = 6379) {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    private function connect(): void
    {
        $this->redis = new Redis();
        try {
            $this->redis->connect($this->host, $this->port);
        } catch (Exception $e) {
            //echo "无法连接到Redis服务器: " . $e->getMessage();
            throw  new Exception("无法连接到Redis服务器: " . $e->getMessage());
        }
    }

    public function set($key, $value, $expiration = 3600) {
        if(is_array($value)) {
            $value  = json_encode($value);
        }
        if(!$expiration){
            return $this->redis->set($key, $value);
        }
        return $this->redis->set($key, $value, $expiration);
    }

    public function isValidJson($string): bool
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function get($key) {
        $Value = $this->redis->get($key);
        if($this->isValidJson($Value)){
            $Value = json_decode($Value,true);
        }
        return $Value;
    }

    public function delete($key) {
        return $this->redis->del($key);
    }

    public function exists($key) {
        return $this->redis->exists($key);
    }

    public function clear() {
        return $this->redis->flushDB();
    }

    public function close(): void
    {
        $this->redis->close();
    }

    public function lock($lockName, $timeout = 10): bool|string
    {
        $lockName = "lock:" . $lockName;
        $token = uniqid('', true);
        // 使用SET命令原子性设置锁和过期时间
        $result = $this->redis->set($lockName, $token, ['nx', 'ex' => $timeout]);
        if ($result) {
            return $token;
        }
        return false;
    }

    public function unlock($lockName, $token)
    {
        $lockName = "lock:" . $lockName;

        $script = "
        if redis.call('GET', KEYS[1]) == ARGV[1] then
            return redis.call('DEL', KEYS[1])
        else
            return 0
        end
    ";

        return $this->redis->eval($script, [$lockName, $token], 1);
    }


    public function __destruct() {
        //echo 'Redis已断开'.PHP_EOL;
        $this->redis->close();
    }











}