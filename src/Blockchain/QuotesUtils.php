<?php

namespace Faj1\Utils\Blockchain;

use Faj1\Utils\CurlHttpClient;
use Swoole\Coroutine;
use function Swoole\Coroutine\run;

class QuotesUtils
{
    /**
     * 利用火币API获取数字货币行情
     * @param string $symbol
     * @return mixed
     */
    public function GetQuotesInfo(string $symbol = "ethusdt"): mixed
    {
        $CurlHttpClient = new CurlHttpClient();
        $return_value =  $CurlHttpClient->get('https://api.huobi.pro/market/detail/merged?symbol='.$symbol);
        return json_decode($return_value,true);
    }


    public function swooleGetQuotesInfo($symbols = ['ethusdt','btcusdt','bnbusdt']): mixed
    {
        $Data = [];
        if (Coroutine::getCid() <= 0) {
            // 如果不在协程环境中，启动一个协程环境
            run(function () use ($symbols, &$Data) {
                $this->handleSymbolsInCoroutine($symbols, $Data);
            });
        } else {
            // 如果已经在协程环境中，直接处理
            $this->handleSymbolsInCoroutine($symbols, $Data);
        }
        return $Data;
    }



    // 抽取协程逻辑为一个独立方法
    private function handleSymbolsInCoroutine(array $symbols, array &$Data): void
    {
        foreach ($symbols as $symbol) {
            go(function () use ($symbol, &$Data) {
                $Data[$symbol] = $this->GetQuotesInfo($symbol);
            });
        }
    }


}
