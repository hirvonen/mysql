<?php

define("APPID", "wxfad891501f5e751d");
define("APPSECRET", "f6300e5f4fcced8daa0dfdcd97cea0d9");
define("JSONMENU", '{
        "button":[
         {
            "name":"我",
            "sub_button":[
            {
                "type":"view",
                "name":"我的订单",
                "url":"http://www.eyoungbeauty.com/e-young/index.php?r=order/orders"
            },
            {
                "type":"view",
                "name":"我的购物车",
                "url":"http://www.eyoungbeauty.com/e-young/index.php?r=cart/cart"
            },
            {
                "type":"view",
                "name":"我的信息",
                "url":"http://www.eyoungbeauty.com/e-young/index.php?r=user/info"
            }]
         },
         {
             "name":"E-YOUNG",
             "sub_button":[
              {
                 "type":"view",
                 "name":"购买疗程",
                 "url":"http://www.eyoungbeauty.com/e-young/index.php?r=commodity/courses"
              },
              {
                 "type":"view",
                 "name":"其他产品",
                 "url":"http://www.eyoungbeauty.com/e-young/index.php?r=commodity/shop"
              },
              {
                 "type":"view",
                 "name":"关于E-YOUNG",
                 "url":"http://www.eyoungbeauty.com/e-young/introduction.html"
              }]
         },
         {
            "type":"click",
            "name":"优惠活动",
            "key":"key_bargan"
         }]
      }');

$weixin = new class_weixin(APPID, APPSECRET);

class class_weixin
{
    //构造函数，获取Access Token并注册menu
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        //获取Access Token
        if($appid && $appsecret){
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        }
        $url_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
        $res = $this->http_request($url_token);
        var_dump($res."<br>");
        $result = json_decode($res, true);
        $this->access_token = $result["access_token"];
        
        //注册menu
        if($this->access_token)
        {
          $this->menu_request($this->access_token);
        }
    }

    private function menu_request($access_token)
    {
      $url_menu = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
      $output = $this->http_request($url_menu,JSONMENU);
      var_dump($output."<br>");
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

?>