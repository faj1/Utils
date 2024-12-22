<?php

namespace Faj1\Utils\database_pool;

use Exception;
use PDO;
use SplQueue;


class PDOConnectionPool
{
    private static ?PDOConnectionPool $instance = null; // 单例实例
    private SplQueue $pool; // 连接池
    private mixed $maxConnections; // 最大连接数
    private int $currentConnections; // 当前连接数
    private $dsn; // 数据源名称
    private $username; // 数据库用户名
    private $password; // 数据库密码
    private mixed $options; // PDO 选项

    // 构造函数, 初始化连接池
    private function __construct($dsn, $username, $password, $options = [], $maxConnections = 500) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->maxConnections = $maxConnections;
        $this->currentConnections = 0;
        $this->pool = new SplQueue(); // 使用 SplQueue 作为队列
    }

    // 禁止克隆
    private function __clone() {}

    // 获取单例实例
    public static function getInstance($dsn, $username, $password, $options = [], $maxConnections = 500): ?PDOConnectionPool
    {
        if (self::$instance === null) {
            self::$instance = new PDOConnectionPool($dsn, $username, $password, $options, $maxConnections);
        }
        return self::$instance;
    }

    // 获取一个连接

    /**
     * @throws Exception
     */
    public function getConnection(): PDO
    {
        if (!$this->pool->isEmpty()) {
            //echo '直接从池子取链接'.PHP_EOL;
            return $this->pool->dequeue();
        }
        if ($this->currentConnections < $this->maxConnections) {
            $this->currentConnections++;
            //echo '链接不够用创建新链接'.PHP_EOL;
            return new PDO($this->dsn, $this->username, $this->password, $this->options);
        }
        throw new Exception('Max connections reached'); // 达到最大连接数
    }

    // 释放连接回池
    public function releaseConnection($connection): void
    {
        $this->pool->enqueue($connection); // 释放连接回队列
    }


    // 关闭所有连接
    public function closeAllConnections(): void
    {
        while (!$this->pool->isEmpty()) {
            $connection = $this->pool->dequeue();
            $connection = null; // 关闭连接
        }
    }

    public function __destruct() {
        //echo '进程关闭'.PHP_EOL;
        $this->closeAllConnections();
        //$this->pool->releaseConnection($this->pdo);
    }

}
