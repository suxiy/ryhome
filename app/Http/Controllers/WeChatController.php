<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Log;

class WeChatController extends BaseController
{
    private $app;
    private $token;
    public function __construct(){
        $this->app = \EasyWeChat::officialAccount(); // 公众号
        $accessToken = $this->app->access_token;
        $this->token = $accessToken->getToken()['access_token'];
    }

    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    public function getCardApiTicket() {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->token . "&type=wx_card";
            $api_ticket = request_Get($url);
            $res = json_decode($api_ticket, true);
            return $res['ticket'];
    }

    public function cardSignature($timestamp, $api_ticket, $noncestr, $card_id, $openid, $code){
        $arr = array();
        $arr['timestamp'] = $timestamp;
        $arr['code'] = $code;
        $arr['nonce_str'] = $noncestr;
        $arr['ticket'] = $api_ticket;
        $arr['openid'] = $openid;
        $arr['card_id'] = $card_id;
//根据官网说明，卡券签名需要的参数有：timestamp（时间戳）,code（卡的编号），nonce_str（随机字符串），ticket（卡券ticket），openid（用户的openid），card_id（卡券的ID）
        sort($arr, SORT_STRING); //排序，这一步一定要有，否则会不成功
        $str = '';
        foreach ($arr as $v) {
            $str .= $v;
        }
//通过foreach组装成字符串
        $signature = sha1($str); //最后通过sha1生成签名
        return array('code' => $code,'openid' =>$openid,'timestamp'=>$timestamp,'nonce_str'=>$noncestr,'signature'=>$signature);
    }

    public function getUserCard() {
        $openid = request()->get('openid');

        $url = "https://api.weixin.qq.com/card/user/getcardlist?access_token=" . $this->token; //调用接口凭证
        $data = array();
        $data['openid'] = $openid;

        //成功
        $list = array();
        $timestamp = time(); //时间戳
        $card_ticket = $this->getCardApiTicket(); //获取卡券API_ticket
        $nonce_str = $this->generateNonceStr(); //随机字符串

        $card_id = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $code = 'A1000088';
        $result = $this->cardSignature($timestamp,$card_ticket,$nonce_str,$card_id,$openid,$code);

        $list['cardId'] = $card_id;
        $list['cardExt'] = json_encode($result);
        return $list;
    }

    public function testCreate(){
        $card = $this->app->card;
        $cardType = 'MEMBER_CARD';
        $attributes = [
//            'background_pic_url'=>'http://asics-connext.oss-cn-zhangjiakou.aliyuncs.com/default/rights/level_bg1.png',
            'base_info'=>[
                'logo_url'=>'http://asics-connext.oss-cn-zhangjiakou.aliyuncs.com/oss/wxlogo.jpg',
                'brand_name'=>'亚瑟士会员卡3',
                'code_type'=>'CODE_TYPE_ONLY_QRCODE',
                'title'=>'微信会员卡',
                'color'=>'Color010',
                'notice'=>'测试提醒',
                'service_phone'=>'400-821-0893',
                'description'=>'卡券使用说明测试描述',
                'date_info'=>[
                    'type'=>'DATE_TYPE_PERMANENT',
                ],
                'sku'=>[
                    'quantity'=>2
                ],
                'get_limit'=>1,
                'use_custom_code'=>true,
                'can_give_friend'=>false,
            ],
            'supply_bonus'=>true,
            'supply_balance'=>false,
            'prerogative'=>'test_prerogative',
            'auto_activate'=>true,
//            "activate_url"=>url('wechat'),
        ];
        $result = $card->create($cardType,$attributes);
        print_r($result);
        exit;
    }



    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve(){
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $this->app->server->push(function($message){
            return sprintf("Name:%s,欢迎关注 ！",$message['FromUserName']);
        });
        $response = $this->app->server->serve();
        return $response->send();
    }

    public function testGet(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $result = $card->get($cardId);
        print_r($result);
        exit;
    }

    public function testList(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $result = $card->list($offset = 0, $count = 10);
        print_r($result);
        exit;
    }

    public function testOpenidList(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $openid  = 'ongXd4g_q6Q4WsdYGpHaxE3Omv84';
        $result = $card->getUserCards($openid, $cardId);
        print_r($result);
        exit;
    }

    public function testDelete(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $result = $card->delete($cardId);
        print_r($result);
        exit;
    }

    public function testDisable(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $code = 'M112233445568';
        $result = $card->code->disable($code, $cardId);;
        print_r($result);
        exit;
    }

    public function testReceive(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        // 领取单张卡券
        $cards = [
            'action_name' => 'QR_CARD',
            'expire_seconds' => 1800,
            'action_info' => [
                'card' => [
                    'card_id' => $cardId,
                    'code' => 'M112233445567',
                    'outer_str' => 'test',
                ],
            ],
        ];
        $result = $card->createQrCode($cards);
        echo "<img src='".$result['show_qrcode_url']."'/>";
        exit;
    }

    public function testUpdate(){
        $card = $this->app->card;
        $cardId = 'pr4nAvmPY3_Nes7mPGjF3uwJSZQg';
        $type = 'MEMBER_CARD';
        $attributes = [
            'base_info' => [

            ],
        ];
        $result = $card->update($cardId, $type, $attributes);
        print_r($result);
        exit;
    }
}
