<?php

namespace Faj1\Utils\Socket;

use React\Socket\UnixServer;

class ReactUnixServer {
    private string $socketPath;

    /**
     * 构造函数，设置 Unix 套接字路径。
     * @param string $socketPath 默认为 /tmp/php-revolt-server.sock
     */
    public function __construct(string $socketPath = '/tmp/p1hp-revolt-server.sock') {
        $this->socketPath = $socketPath;
        // 如果套接字文件已存在，先删除它
        if (file_exists($this->socketPath)) {
            if (@stream_socket_client("unix://{$this->socketPath}")) {
                die("套接字文件仍在使用中，无法清除。请检查是否有其他进程运行。\n");
            }
            unlink($this->socketPath);
        }
    }
    /**
     * 启动服务器，监听客户端连接。
     */
    public function start(): void {
        $server = new UnixServer( $this->socketPath);
        $server->on('connection', function (\React\Socket\ConnectionInterface $connection) {
            echo 'New connection' . PHP_EOL;
            $connection->write('hello there!' . PHP_EOL);
            // 写入欢迎信息
            $connection->write("Hello " . $connection->getRemoteAddress() . "! \n");
            $connection->write("Welcome to this amazing server! \n");
            $connection->write("Here's a tip: don't say anything. \n");
            // 监听数据并关闭连接
            $connection->on('data', function ($data) use ($connection) {
                var_dump($data);
                $connection->close();
            });
        });
    }




}
