<?php
class Ui
{
    function __construct()
    {
        $this->apiKey = '6f8b921612184826a7a0052c59faa1a1';
        $this->secret = '3924b3c2152ee53e';
        $this->selfInfo = ['location' => ['city' => '广州']];
        $this->userId = md5(time());
        $this->timestamp = time();
        $this->text = 111;
    }

    function text($text){
        $this->text = $text;
        $iv = str_repeat(chr(0),16);
        $aesKey = md5($this->secret.$this->timestamp.$this->apiKey);
        $param = [
            'perception' => [
                'inputText' => [
                    'text' => $this->text,
                ],
                'selfInfo' => $this->selfInfo
            ],
            'userInfo' => [
                'apiKey' => $this->apiKey,
                'userId' => $this->userId,
            ]
        ];
        $cipher = base64_encode(openssl_encrypt(json_encode($param), 'aes-128-cbc', hash('MD5', $aesKey, true), OPENSSL_RAW_DATA, $iv));

        $postData = [
            'key' => $this->apiKey,
            'timestamp' => $this->timestamp,
            'data' => $cipher
        ];

        $result = json_decode($this->post('http://openapi.tuling123.com/openapi/api/v2',json_encode($postData)));
        return $result->results[0]->values->text;
    }

    private function post($url,$data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
$ui = new Ui();
echo isset($_REQUEST['text'])?$ui->text($_REQUEST['text']):'无反应！！';