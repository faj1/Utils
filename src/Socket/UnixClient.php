<?php

namespace Faj1\Utils\Socket;

use Faj1\Utils\SocketUtils;

class UnixClient
{

    public function send($socketPath = '/tmp/php-revolt-server.sock',$MessageData = ['method'=>'handle','params'=>'xxxxxxxxxx'])
    {

        // 检查套接字文件是否存在
        if (!file_exists($socketPath)) {
            throw new \Exception("Socket file {$socketPath} does not exist.");
        }
        $maxRetries = 40; // 最大重试次数
        $retryDelay = 500000; // 每次重试的间隔时间（微秒）
        $client = false;
        for ($i = 0; $i < $maxRetries; $i++) {
            $client = @stream_socket_client('unix://' . $socketPath, $errno, $errstr);
            if ($client) {
                break; // 如果连接成功，则跳出重试循环
            }
            usleep($retryDelay); // 延迟一定时间后再重试
        }
        if (!$client) {
            throw new \Exception("Error: Unable to connect to the server after $maxRetries attempts.");
        }

        $data = SocketUtils::Packet($MessageData);
        fwrite($client, $data);
        $response = SocketUtils::UnixUnpack($client);
        echo "Response from server: " . $response . PHP_EOL;
        // 关闭连接
        fclose($client);
    }


}
