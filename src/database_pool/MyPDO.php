<?php

namespace Faj1\Utils\database_pool;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use PDO;


class MyPDO
{
    private PDOConnectionPool $pool;
    private PDO $pdo;
    public function GetManager($Config = []): MySqlConnection|Connection
    {
        $this->pool = PDOConnectionPool::getInstance('mysql:host=' . $Config['host'] . ';port=' . $Config['port'] . ';dbname=' . $Config['database'], $Config['username'], $Config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 设置错误模式为抛出异常
            ////PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 设置默认获取模式为关联数组
            //PDO::ATTR_PERSISTENT => true, // 使用持久连接
            PDO::ATTR_STRINGIFY_FETCHES => false, // 禁止将数值转换为字符串
            PDO::ATTR_CASE => PDO::CASE_NATURAL, // 保持字段名的大小写
            PDO::ATTR_EMULATE_PREPARES => false // 关闭预处理语句模拟模式
        ]);
        $this->pdo = $this->pool->getConnection();
        //var_dump('获取到PDO', $this->pdo);
        return new MySqlConnection($this->pdo);
    }

    public function __destruct() {
        //echo '自动归还连接到池子'.PHP_EOL;
        $this->pool->releaseConnection($this->pdo);
    }

}
