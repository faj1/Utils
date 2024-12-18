<?php

namespace Faj1\Utils;

use Exception;

class SocketUtils
{

    /**
     * 数据封包
     * @param $data
     * @return string
     * @throws Exception
     */
    public static function Packet($data): string
    {
        // 将数据转换为二进制字符串
        $body = json_encode($data,JSON_UNESCAPED_UNICODE);
        // 计算包的总长度（包含4个字节的长度本身）
        $body = gzcompress($body);
        $totalLength = strlen($body);
        // 检查总长度是否超过最大包长度
        if ($totalLength > 2000000) {
            throw new Exception("数据包超过最大长度限制");
        }
        // 构建数据包：包长度 + 数据体
        return pack('N', $totalLength) . $body;
    }



    /**
     * 数据解包
     * @param $data
     * @return mixed
     */
    public static function Unpack($data): mixed
    {
        $header = substr($data, 0, 4);
        $dataLength = unpack('N', $header)[1];
        // 读取实际的数据内容
        $body = substr($data, 4, $dataLength);
        $data = gzuncompress($body);
        return json_decode($data, true);
    }



    public static function UnixUnpack($client,$debug = false)
    {
        $header = @fread($client, 4);
        if(strlen($header) != 4) {
            return [];
        }
        $dataLength = unpack('N', $header)[1];
        if($debug){
            echo "消息总长度:。{$dataLength}\n";
        }
        $body = @fread($client, $dataLength);
        $data = gzuncompress($body);
        if($debug){
            echo "消息内容::。{$data}\n";
        }
        return json_decode($data, true);
    }









}
