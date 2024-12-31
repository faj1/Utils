<?php

namespace Faj1\Utils;

class RedisUtils
{

    public function GetRedis($host,$port): \Redis
    {
        $redis = new \Redis();
        $redis->connect($host, $port);
        return $redis;
    }



    public function GetDistributedId($redis)
    {

        // 指定一个KEY用作ID生成器
        $key = 'distributed_id_generator';
        // 使用Redis的自增计数器生成唯一ID
        return $redis->incr($key);
    }

}
