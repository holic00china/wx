<?php
include './Base.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of notify
 *
 * @author R
 */
class Notify extends Base{
    public function __construct() {
        parent::__construct();
        //获取微信服务器提交过来的通知数据
        $xml= $this->getPost();
        //将XML格式的数据转换为数组
        $arr= $this->XmlToArr($xml);
        //验证签名
         if($this->checkSign($arr))
         {
            //验证订单金额
            if($this->checkPrice($arr)){
                //更新订单状态 应该返回到数据表中
                $this->logs('log.txt', '订单支付成功，对应的订单号为'.$arr['out_trade_no']);
                $params=[
                  'return_code'=>'SUCCESS',
                    'return_msg' =>'ok'
                ];
                echo $this->ArrToXml($params);
            }
         }
    }
            //根据订单号$arr['out_trade_no']在商户系统中查询订单金额 并和$arr['total_fee']作比较
    public function checkPrice($arr)
    {
        if($arr['return_code']=='SUCCESS' && $arr['result_code']=='SUCCESS')
        {
            if($arr['total_fee']==2)//订单号应在数据库查询
            {
                return true;
            }else{
                $this->logs('log.txt', '订单金额不匹配 微信支付系统提交过来的金额为'.$arr['total_fee']);
            }
        }
        else
        {
            $this->logs('log.txt', '通知状态有误！');
        }
    }
}
new Notify();
