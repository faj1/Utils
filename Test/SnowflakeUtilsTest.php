<?php

namespace Faj1\Utils\Test;

use Faj1\Utils\Blockchain\QuotesUtils;
use Faj1\Utils\database_pool\MyPDO;
use Faj1\Utils\SnowflakeUtils;
use Faj1\Utils\Socket\RevoltUnixServer;
use Faj1\Utils\Socket\UnixClient;
use PHPUnit\Framework\TestCase;
use function Swoole\Coroutine\run;

class SnowflakeUtilsTest extends TestCase
{

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

    public function hasDuplicates(array $arr): bool {
        $valueCounts = array_count_values($arr);
        foreach ($valueCounts as $value => $count) {
            if ($count > 1) {
                return true; // 存在重复值
            }
        }
        return false; // 没有重复值
    }

    public function testSocket(){

        run(function () use (&$UnixClient) {
            for ($i = 0; $i < 10; $i++) {
                go(function () {
                    $UnixClient = new UnixClient();
                    $UnixClient->send('/tmp/telegram_be6e42caa17bd01f9df26475eb11f1a6b1afa574b14e42dad046a11b65805438.sock',['method'=>'TcpSendMessage','params'=>['peer'=>'@bifa03','message'=>'HH1111111111111111HH']]);
                });
            }
        });
        $this->assertTrue(true, 'Code executed successfully without exceptions.');
    }

    public function testDbPool(){
        $MyPDO = new MyPDO();

        run(function () use (&$MyPDO) {
               for ($i = 0; $i < 300; $i++) {
                      go(function ()use ($MyPDO) {
                          $Config = [
                              'driver'      => 'mysql',
                              'host'        => '127.0.0.1',
                              'port'        => '3306',
                              'database'    => 'tgbotxin',
                              'username'    => 'tgbotxin',
                              'password'    => 'MKMTGx4jeRai7t37',
                              'charset'     => 'utf8mb4',
                              'collation'   => 'utf8mb4_general_ci',
                              'prefix'      => '',
                              'strict'      => true,
                              'engine'      => null,
                          ];
                          $Manager = $MyPDO->GetManager($Config);
                          $user_center_invitation_code = $Manager->table('user_center_invitation_code')->where([])->first();
                          var_dump($user_center_invitation_code);
                      });
               }
        });

         $this->assertTrue(true, 'Code executed successfully without exceptions.');
    }


    public function testGetQuotesInfo()
    {
        $QuotesUtils = new QuotesUtils();
        $QuotesUtils->swooleGetQuotesInfo();
        $this->assertTrue(true, 'Code executed successfully without exceptions.');
    }


}
