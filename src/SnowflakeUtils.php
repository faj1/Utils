<?php

namespace Faj1\Utils;

use Godruoyi\Snowflake\RedisSequenceResolver;
use Godruoyi\Snowflake\Snowflake;

/**
 * 雪花ID工具
 */
class SnowflakeUtils
{



    public static function GetId($redis = null,$datacenterId = 0, $workerId =1): string
    {
        $snowflake = new Snowflake($datacenterId, $workerId);
        $snowflake->setStartTimeStamp(strtotime('2024-09-09')*1000); // millisecond
        if(!$redis){
//            $redis = new \Redis();
//            $redis->connect('127.0.0.1', 6379);
            $RedisUtils = new RedisUtils();
            $redis = $RedisUtils->GetRedis('127.0.0.1', 6379);
        }

        $snowflake->setSequenceResolver(new RedisSequenceResolver($redis));
        $id = $snowflake->id();
        $redis->close();
        return $snowflake->id();
    }


}
