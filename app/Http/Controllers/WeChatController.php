<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Log;
use App\Http\Helpers\Api\ApiResponse;

class WeChatController extends BaseController
{
    use ApiResponse;

    /**
     * @var \App\Lib\WechatCard mixed
     */
    private $app;
    private $token;
    public function __construct(){
//        $this->app = \EasyWeChat::officialAccount(); // 公众号
//        $accessToken = $this->app->access_token;
//        $this->token = $accessToken->getToken()['access_token'];
        $this->app = app()->make(\App\Lib\WechatCard::class);
    }

    public function serve(Request $request){
        return $this->success();
    }

    public function activePage(Request $request){
        $data = $request->all();
        log_array('api','wechat_active',$data);
        print_r($data);
        exit;
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

        $card_id = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $code = 'A1000088';
        $result = $this->cardSignature($timestamp,$card_ticket,$nonce_str,$card_id,$openid,$code);

        $list['cardId'] = $card_id;
        $list['cardExt'] = json_encode($result);
        return $list;
    }

    public function testCreate(){
        $card = $this->app->createCard();
        $code = 'M'.uniqid();
        $url = $this->app->createQrCode($card,$code);
        echo "<img src='".$url."'/>";
    }

    public function testGet(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $result = $card->get($cardId);
        print_r($result);
        exit;
    }

    public function testList(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $result = $card->list($offset = 0, $count = 10);
        print_r($result);
        exit;
    }

    public function testOpenidList(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $openid  = 'ongXd4g_q6Q4WsdYGpHaxE3Omv84';
        $result = $card->getUserCards($openid, $cardId);
        print_r($result);
        exit;
    }

    public function testDelete(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $result = $card->delete($cardId);
        print_r($result);
        exit;
    }

    public function testDisable(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
        $code = 'M112233445568';
        $result = $card->code->disable($code, $cardId);;
        print_r($result);
        exit;
    }

    public function testReceive(){
        try{
            $card_id = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
            $code = 'M112233445561';
            $url = $this->app->createQrCode($card_id,$code);
            echo "<img src='".$url."'/>";
            exit;
        }catch (\Exception $e){
            die($e->getMessage());
        }
    }

    public function testUpdate(){
        $card = $this->app->card;
        $cardId = 'pr4nAvsf_D2iOr8tbcoGVltF1wDk';
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
