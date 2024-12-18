<?php

namespace Faj1\Utils\Socket;

// 定义一个类，包含回调函数
class CallbackExample {
    private $message;

    public function __construct($message) {
        $this->message = $message;
    }

    // 定义一个普通方法，可以作为回调使用
    public function handle($data) {
        echo $this->message . ": " . $data . PHP_EOL;
    }

    // 静态方法也可以作为回调使用
    public static function staticHandle($data) {
        echo "Static callback received: " . $data . PHP_EOL;
    }
}
