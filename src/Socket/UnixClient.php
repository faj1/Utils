<?php

namespace Faj1\Utils\Socket;

use Faj1\Utils\SocketUtils;

class UnixClient
{

    public function send()
    {
        $socketPath = '/tmp/p1hp-revolt-server.sock'; // 与服务器端保持一致的套接字文件路径
        // 检查套接字文件是否存在
        if (!file_exists($socketPath)) {
            echo "Error: Socket file {$socketPath} does not exist." . PHP_EOL;
            exit(1);
        }
        // 创建客户端套接字连接
        if (!$client) {
            echo "Error: Unable to connect to the server: $errstr ($errno)" . PHP_EOL;

        }
        if ($client === false) {
            echo "无法连接到服务器: $errstr ($errno)\n";
            return; // 或者抛出异常
        }


        // 准备发送的数据
        $data = json_encode([
            'params' => ['@bifa001', 'This is a test message.']
        ]);
        $data = SocketUtils::Packet(['method'=>'sendMessage','params'=>['@bifa03','测试']]);
        fwrite($client, $data);
        //echo "Data sent to server successfully." . PHP_EOL;
        // 接收服务器的响应（如果需要）
        $response = @fread($client, 1024);
        if ($response) {
           // echo "Response from server: " . $response . PHP_EOL;
        } else {
//echo "No response received from server." . PHP_EOL;
        }
        // 关闭连接
        fclose($client);
    }


}
