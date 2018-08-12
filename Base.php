<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author R
 */
class Base {
    const KEY= 'XzjCzy1234567890abcdefghijklmnOP';
    const APPID='wx732104e17a55d175';
    const MCHID='1508962121';
    const SERCET='0262130c36b1e8ad05777311ce2a254f';
    const UOURL='https://api.mch.weixin.qq.com/pay/unifiedorder';
     const NOTIFY = 'http://www.xueyansmt.com/application/weixin/notify.php'; 
    public function __construct() { 
    }
    public  function getSign($arr) 
    {       
        //去除空值
        array_filter($arr);
        if(isset($arr['sign']))
        {
            unset($arr['sign']);
        }
        //排序
        ksort($arr);
        //组装字符串+key
        $str= $this->arrToUrl($arr). '&key=' .self::KEY;
        //使用MD5加密
        return strtoupper(md5($str));
        //转换成大写
    }
    //获取带签名的数组
    public function setSign($arr) {
         $sign= $this->getSign($arr);
      $arr['sign']=$sign;
      return $arr;
    }
    //数组转换URL 不带key
    public function arrToUrl($arr) {
       return urldecode(http_build_query($arr));
        
    }
    public function checkSign($arr) {
        //生成新签名
        $sign=  $this->getSign($arr);
        //和数组中原始签名进行比较
        if($sign==$arr['sign'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function logs($file,$data) {
        $data= is_array($data) ? print_r($data,true) : $data;
        file_put_contents('./logs/' .$file, $data);
    }
    public function getPost()
    {
        return file_get_contents('php://input');
    }
    //XML转数组
    public function XmlToArr($xml)
    {
        if($xml=='')            return '';
        libxml_disable_entity_loader(true);
        $arr= json_encode(simplexml_load_string($xml,'SimpleXMLElemet',LIBXML_NOCDATA));
        return $arr;
        }
        //数组转换xml
         public function ArrToXml($arr)
    {
        if(!is_array($arr) || count($arr) == 0) return '';

        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
                if (is_numeric($val)){
                        $xml.="<".$key.">".$val."</".$key.">";
                }else{
                        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
        }
        $xml.="</xml>";
        return $xml; 
    }
    //post字符串到接口
    public function postStr($url,$postfields) {
           $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_SSL_VERIFYPEER] = false;//禁用证书校验
	$params[CURLOPT_SSL_VERIFYHOST] = false;
        $params[CURLOPT_POSTFIELDS] = $postfields;
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
    //统一下单
    public function unfiedorder($params) {
            //获取到带签名的数组
             $params=$this->setSign($params);
            //数组转xml
            $xml= $this->ArrToXml($params);
            //发送数据到统一下单API
            $data= $this->postStr(self::UOURL, $xml);
            $arr= $this->XmlToArr($data);
            if($arr['result_code']=='SUCCESS' && $arr['return_code']=='SUCCESS')
            {
                return $arr;
            }
            else {
                $this->logs('error.txt', $data);
                return false;}
    }
}
