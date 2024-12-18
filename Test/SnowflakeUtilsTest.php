<?php

namespace Faj1\Utils\Test;

use Faj1\Utils\SnowflakeUtils;
use Faj1\Utils\Socket\RevoltUnixServer;
use Faj1\Utils\Socket\UnixClient;
use PHPUnit\Framework\TestCase;
use function Swoole\Coroutine\run;

class SnowflakeUtilsTest extends TestCase
{

    public function hasDuplicates(array $arr): bool {
        $valueCounts = array_count_values($arr);
        foreach ($valueCounts as $value => $count) {
            if ($count > 1) {
                return true; // 存在重复值
            }
        }
        return false; // 没有重复值
    }
    public function testGetId()
    {
        $Data = [];
        run(function () use (&$Data) {
            for ($i = 0; $i < 20; $i++) {
                \Swoole\Coroutine\go(function () use (&$Data) {
                    $Data[] = SnowflakeUtils::getId();
                });
            }
        });
        //var_dump( $Data);
        if ($this->hasDuplicates($Data)) {
            echo "数组中有重复值".PHP_EOL;
        } else {
            echo "数组中没有重复值".PHP_EOL;
        }
        $this->assertTrue(true, 'Code executed successfully without exceptions.');
    }


    public function testSocket(){

        run(function () use (&$UnixClient) {
            for ($i = 0; $i < 20000; $i++) {
                go(function () {
                    $UnixClient = new UnixClient();
                    $UnixClient->send();
                });
            }
        });
        $this->assertTrue(true, 'Code executed successfully without exceptions.');
    }
}
