<?php

use Faj1\Utils\Socket\UnixClient;
use function Swoole\Coroutine\run;

require 'vendor/autoload.php';
run(function () use (&$UnixClient) {
    for ($i = 0; $i < 200; $i++) {
        go(function () {
            $UnixClient = new UnixClient();
            $UnixClient->send();
        });
    }
});
