<?php
include './Base.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of callback
 *
 * @author R
 */
class WeiXinback extends Base {
    public function __construct() {
        parent::__construct();
        //接收微信服务器发送的数据
        $data= $this->getPost();
        $arr= $this->XmlToArr($data);
        //记录数据
       // $this->logs('logs.txt', $arr);
        //验证签名
       if( $this->checkSign($arr))
       {
           //调用统一下单API
           $params=[
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'product_id' =>$arr['product_id'],
            'nonce_str' => md5(time()),
               'body' =>'尿素测试支付',
               'out_trade_no'=>1122,
               'total_fee'=>2,
               'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],
               'notify_url' => self::NOTIFY,
               'trade_type' =>'NATIVE'
           ];
           $arr= $this->unfiedorder($params);
           $return_params=[
               'return_code' =>'SUCCESS',
               'appid' =>self::APPID,
               'mch_id' => self::MCHID,
               'nonce_str' =>md5(time()),
               'prepay_id' =>$arr['prepay_id'],
               'result_code' =>'SUCCESS'
           ];
           $return_params= $this->setSign($return_params0);
           $retutn_xml= $this->ArrToXml($return_params);
           echo $retutn_xml;
           //获取到prepay_id
           //返回prepay_id等数据
           $this->logs('logs.txt', $retutn_xml);
       }
       else
       {
            $this->logs('logs.txt', '签名校验失败');
       }
    }
}
new WeiXinback();