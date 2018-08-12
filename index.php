<?php
include './Base.php';
include './phpqrcode/phpqrcode.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author R
 */
class WeiXinPay extends Base {
    public function checks() {
        $params=[
            'appid' => 'wx732104e17a55d175',
            'mch_id' => '1508962121',
            'body' => 'test',
            'sign' =>'D76FFBA66056B1D07405EB11EFE15418'
        ];
        if($this->checkSign($params))
        {
            echo '签名校验通过';
        }
        else
        {
            echo '签名校验失败';
        }
    }
    public function getQRurl($oid) {
        $params=[
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'product_id' =>$oid,
            'time_stamp' => time(),
            'nonce_str' => md5(time())
        ];
        return 'weixin://wxpay/bizpayurl?' . $this->arrToUrl($this->setSign($params));
    }
}
$obj=new WeiXinPay();
$result= $obj->getQRurl('1234');
QRcode::png($result);
//$obj->checks();