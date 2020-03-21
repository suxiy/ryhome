<?php namespace App\Http\Controllers;

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
//        $data = $request->all();
//        log_array('api','wechat',$data);
//        $result = $this->checkSignature($data);
//        log_array('api','wechat',$result);
        $msg = request()->getContent();
        $xmlObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        log_array('api','wechat',$xmlObj);
        return true;
//        return $this->success();
    }

    private function checkSignature($data)
    {
        $echoStr  = $data[ "echostr" ];
        $signature = $data["signature"];
        $timestamp = $data["timestamp"];
        $nonce = $data["nonce"];

        $token = 'wxthytrdh';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return $echoStr;
        }else{
            return false;
        }
    }

    public function getUserCard() {
        $openid = request()->get('openid');

        $url = "https://api.weixin.qq.com/card/user/getcardlist?access_token=" . $this->token; //调用接口凭证
        $data = array();
        $data['openid'] = $openid;

        //成功
        $list = array();
        $timestamp = time(); //时间戳
        $card_ticket = $this->app->getCardApiTicket(); //获取卡券API_ticket
        $nonce_str = $this->app->generateNonceStr(); //随机字符串

        $card_id = 'pr4nAvofzAnFMK9G0NRftkgqbNyI';
        $result = $this->app->cardSignature($timestamp,$card_ticket,$nonce_str,$card_id,$openid);

        $list['cardId'] = $card_id;
        $list['cardExt'] = json_encode($result);
        return $list;
    }

    public function testCreate(){
        $card = $this->app->createCard();
        echo $card;
    }

    public function testCreateQr(){
        $card = $this->app->createCard();
        $code = 'M'.uniqid();
        $url = $this->app->createQrCode($card,$code);
        echo "<img src='".$url."'/>";
    }

    public function activePage(Request $request){
        $data = $request->all();
        log_array('api','wechat_active',$data);
        $code = '746290963759';
        $member_code = 'B'.uniqid();
        $result = $this->app->active($data['card_id'],$code,$member_code);
        log_array('api','wechat_active',$result);
        return view("front.wechat.getCard");
    }

    public function cardUpdate(Request $request){
        $result = $this->app->update();
        log_array('api','wechat_update',$result);
        print_r($result);
        exit;
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

    public function clear(){
        return $this->app->clear();
    }

    public function upload(){
        print_r($this->app->upload());
        exit;
    }

    public function query(){
        $list = $this->app->query();
        $card_list = object_get($list,'card_list');
        if($card_list){
            foreach($card_list as $item){
                $card_id = $item->card_id;
                $code = $item->code;
//                $find_resp = $this->app->find($card_id,$code);
                $resp = $this->app->del($card_id,$code);
            }
        }
        exit;
    }

}
