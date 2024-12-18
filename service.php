<?php
require 'vendor/autoload.php';
$CallbackExample = new \Faj1\Utils\Socket\CallbackExample('回调测试');
$RevoltUnixServer = new \Faj1\Utils\Socket\RevoltUnixServer($CallbackExample);
$RevoltUnixServer->start();
