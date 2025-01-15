<?php

namespace Faj1\Utils\Trans;
use Faj1\Utils\CurlHttpClient;

/**
 * 翻译之家相关接口
 */
class TransHome
{

    private string $DeepKey = "";
    private string $GoogleKey = "";

    private int $Retries = 5;

    public function __construct($DeepKey, $GoogleKey)
    {
        $this->DeepKey = $DeepKey;
        $this->GoogleKey = $GoogleKey;
    }


    public string $translateUrl = "https://www.trans-home.com/api/index/translate?token=";


    public function translate($keywords, $sourceLanguage, $targetLanguage, $code = "Deep",$number = 0)
    {
        $Url  = "";
        if ($code == "Deep") {
            $Url = $this->translateUrl . $this->DeepKey;
        } else if ($code == "Google") {
            $Url = $this->translateUrl . $this->GoogleKey;
        }
        $CurlHttpClient = new CurlHttpClient();
        $Value = $CurlHttpClient->post($Url,['keywords' => $keywords, 'sourceLanguage' => $sourceLanguage, 'targetLanguage' => $targetLanguage]);
        $Data = json_decode($Value, true);
        if(!isset($Data["data"]['text']) and $number < $this->Retries) {
            usleep(80000);
            $number++;
            $this->translate($keywords, $sourceLanguage, $targetLanguage, $code, $number);
        }
        return $Data;
    }

}
