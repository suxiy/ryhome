<?php
/**
 * Created by PhpStorm.
 * User: SUNS017
 * Date: 2019/7/17
 * Time: 11:49
 */

namespace App\Lib;

use EasyWeChat\Factory;
use Illuminate\Support\Facades\DB;

class Wechat
{
    private $app_id;
    private $secret;
    private $mch_id;
    private $key;

    public function __construct(){
        $this->app_id = 'wxd31aecbc4af3deb2';
        $this->secret = 'f826d560b1513b1d2f012adfaaa8ea2a';
        $this->mch_id = '1501524791';
        $this->key = 'geqpgDzacMttg4BhsiqFk7aPAD1gzAwl';
    }

    public function code2Session($code){
        $url = sprintf('https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',$this->app_id,$this->secret,$code);
        $resp = curl_get($url);
        return json_decode($resp,true);
    }

    private function getPayment(){
        $config = [
            'app_id'             => $this->app_id,
            'mch_id'             => $this->mch_id,
            'key'                => $this->key,
        ];
        return Factory::payment($config);
    }

    public function unifiedOrder($openid,$order_id){
        $app = $this->getPayment();

        $result = $app->order->unify([
            'body' => '瑞源预算',
            'out_trade_no' => (string)$order_id,
            'total_fee' => 1,
            'notify_url' => route('api.wechat.notify'),
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ]);

        if( $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            $result = $app->jssdk->bridgeConfig($result['prepay_id'],false);//第二次签名
            return $result;
        }
        return false;
    }

    public function notify(){
        $config = [
            'app_id'             => $this->app_id,
            'mch_id'             => $this->mch_id,
            'key'                => $this->key,
        ];
        $app = Factory::payment($config);

        $app->handlePaidNotify(function($message, $fail){
            $log['RequestData'] = $message;
            log_json('api','wechatNotify',json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            //{"appid":"wxd31aecbc4af3deb2","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1501524791","nonce_str":"5d41aec971ac0","openid":"oQQmP4r_I98is43VOtSpZ6ngQRwE","out_trade_no":"D1564585672853","result_code":"SUCCESS","return_code":"SUCCESS","sign":"9B209E482FB327AAF985753926D46259","time_end":"20190731230757","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000353201907317513062970"}

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $orderModel = DB::table('app_project')
                ->where('id',$message['out_trade_no']);
            $order = $orderModel->first();

            if (!$order || $order->paid_status) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $orderModel->update(['paid_status'=>1,'transaction_id'=>$message['transaction_id']]);
                    // 用户支付失败
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
    }


}