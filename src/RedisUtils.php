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

}
