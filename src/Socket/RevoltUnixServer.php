<?php

namespace Faj1\Utils\Socket;

use Revolt\EventLoop;

class RevoltUnixServer {
    private string $socketPath;
    private $server;

    /**
     * 构造函数，设置 Unix 套接字路径。
     *
     * @param string $socketPath 默认为 /tmp/php-revolt-server.sock
     */
    public function __construct(string $socketPath = '/tmp/php-revolt-server.sock') {
        $this->socketPath = $socketPath;
    }

    /**
     * 启动服务器，监听客户端连接。
     */
    public function start(): void {
        // 如果套接字文件已存在，先删除它
        if (file_exists($this->socketPath)) {
            if (@stream_socket_client("unix://{$this->socketPath}")) {
                die("套接字文件仍在使用中，无法清除。请检查是否有其他进程运行。\n");
            }
            unlink($this->socketPath);
        }
        // 创建 Unix 套接字服务器
        $this->server = @stream_socket_server("unix://{$this->socketPath}", $errno, $errstr);
        if (!$this->server) {
            die("无法创建服务器套接字: $errstr ($errno)\n");
        }

        // 设置套接字权限
        chmod($this->socketPath, 0777);

        echo "服务器已启动，等待客户端连接...\n";

        // 注册服务器监听器，当有客户端连接时调用
        EventLoop::onReadable($this->server, function ($callbackId, $server) {
            $client = @stream_socket_accept($server);
            if ($client) {
                echo "新客户端已连接！\n";
                $this->handleClient($client);
            }
        });

        // 开始事件循环
        EventLoop::run();
    }

    /**
     * 处理客户端的连接及消息传递。
     *
     * @param $newClient
     */
    private function handleClient(&$newClient): void {
        // 注册客户端监听器，当有数据可读时调用
        stream_set_blocking($newClient, false);
        $watcherId = EventLoop::onReadable($newClient, function ($callbackId, $client) use (&$newClient) {
            $data = fread($client, 1024);
            // 如果读取失败或客户端断开连接，清理资源
            if ($data === false || $data === '') {
                echo "客户端断开连接。\n";
                fclose($newClient);
                EventLoop::cancel($callbackId);
            } else {
                echo "收到消息：$data";
                // 发送响应回客户端
                fwrite($client, "服务器收到：$data");
            }
        });
    }

    /**
     * 析构函数，清理服务器资源。
     */
    public function __destruct() {
        if (isset($this->server)) {
            fclose($this->server);
            unlink($this->socketPath);
        }
    }
}
