<?php

namespace Faj1\Utils\Blockchain;

use Faj1\Utils\CurlHttpClient;

/**
 * 法币行情服务
 */
class FiatCurrencyUtils
{
    /**
     * 利用欧易API获取数字货币场外交易行情
     * @param int $Limit
     * @param bool $debug
     * @return array
     */
    public function GetQuotesInfo(int $Limit = 10, bool $debug = false): array
    {
        $CurlHttpClient = new CurlHttpClient();
        $return_value =  $CurlHttpClient->get('https://www.okx.com/v3/c2c/tradingOrders/books?quoteCurrency=CNY&baseCurrency=USDT&side=buy&paymentMethod=all&userType=all&receivingAds=false&t=1736433428013');
        $Data = [];
        $NewData = [];
        $Data = json_decode($return_value, true);
        if(isset($Data["data"]["buy"]) and  $Data["data"]["buy"]){
            foreach ($Data["data"]["buy"] as $value) {
                if(!isset($NewData['all']) or count($NewData['all']) < $Limit){
                    $NewData['all'][] = $value;
                }
                if(isset($value['paymentMethods']) and  $value['paymentMethods']){
                    foreach ($value['paymentMethods'] as $paymentMethod) {
                        if($paymentMethod === "bank"){
                            if(!isset($NewData['bank']) or count($NewData['bank']) < $Limit){
                                $NewData['bank'][] = $value;
                            }
                        }
                        if($paymentMethod === "aliPay"){

                            if(!isset($NewData['aliPay']) or count($NewData['aliPay']) < $Limit){
                                $NewData['aliPay'][] = $value;
                            }
                        }
                        if($paymentMethod === "wxPay"){
                            if(!isset($NewData['wxPay']) or count($NewData['wxPay']) < $Limit){
                                $NewData['wxPay'][] = $value;
                            }
                        }
                    }
                }
            }
        }
        if($debug){
            echo "结果:".json_encode($NewData)."\n";
        }
        return ['code'=>0,'msg'=>'ok','data'=>$NewData];
    }




}
