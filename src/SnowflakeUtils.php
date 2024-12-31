<?php

namespace Faj1\Utils;

use Godruoyi\Snowflake\RedisSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Godruoyi\Snowflake\SnowflakeException;

/**
 * 雪花ID工具
 */
class SnowflakeUtils
{



    /**
     * 取雪花算法订单号
     * @param $redis
     * @param int $datacenterId
     * @param int $workerId
     * @return string
     * @throws SnowflakeException
     */
    public static function GetId($redis = null, int $datacenterId = 0, int $workerId =1): string
    {

        if(!$redis){
            $RedisUtils = new RedisUtils();
            $redis = $RedisUtils->GetRedis('127.0.0.1', 6379);
        }
        $snowflake = new Snowflake($datacenterId, $workerId);
        $snowflake->setStartTimeStamp(strtotime('2024-09-09')*1000); // millisecond
        $snowflake->setSequenceResolver(new RedisSequenceResolver($redis));
        $id = $snowflake->id();
        $redis->close();
        return $id;
    }

    /**
     * 取指定长度的随机字符串
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length = 10): string
    {
        // 定义允许的字符
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        // 循环生成随机字符串
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}
