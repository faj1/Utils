<?php

namespace Faj1\Utils;

class StringUtils
{
    /**
     * 用于加密字符串
     * @param $text
     * @return string
     */
    public static function encryption($text): string
    {
        $length = strlen($text); // 获取字符串长度

        if ($length <= 4) {
            // 如果长度小于等于4，替换成全 '*'
            return str_repeat('*', $length);
        } elseif ($length >= 5 && $length <= 7) {
            // 如果长度是 4 到 7，保留前1后1位，其余替换成 '*'
            return substr($text, 0, 1) . str_repeat('*', $length - 2) . substr($text, -1);
        } elseif ($length >= 8 && $length <= 10) {
            // 如果长度是 8 到 10，保留前2后2位，其余替换成 '*'
            return substr($text, 0, 2) . str_repeat('*', $length - 4) . substr($text, -2);
        } else {
            // 如果长度超过10，保留前3后3位，其余替换成 '*'
            return substr($text, 0, 3) . str_repeat('*', $length - 6) . substr($text, -3);
        }
    }
}
