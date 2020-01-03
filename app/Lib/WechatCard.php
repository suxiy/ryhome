<?php namespace App\Lib;

use Curl\Curl;

class WechatCard
{
    private $appid;
    private $secret;
    protected $curl;
    private $token;

    public function __construct(){
        $this->appid = 'wxe21bf17930bb1463';
        $this->secret = '5c748c8e898c29d32a15072a4c48d7a3';
        $this->curl = new Curl();
        try{
            $this->getAccessToken();
        }catch (\Exception $e){
            die($e->getMessage());
        }
    }

    private function getAccessToken($force = 0):void{
        $time = time();
        $file = __DIR__.'/token';
        if(!$force){
            $json = is_file($file)?file_get_contents($file):null;
            if($json){
                $array = json_decode($json,true);
                //2小时内返回旧token
                if($time<$array['time']){
                    $this->token = $array['token'];
                    return;
                }
            }
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $data = [
            'appid'=>$this->appid,
            'secret'=>$this->secret,
            'grant_type'=>'client_credential',
        ];
        call_user_func([$this->curl,'get'],$url,$data);
        $resp = $this->curl->response;
        if(!empty($resp) && !object_get($resp,'errcode')){
            $token = object_get($resp,'access_token');
            $time = $time+object_get($resp,'expires_in');
            file_put_contents($file,json_encode(compact('token','time')));
        }else{
            $errorMsg = empty($resp)?($this->curl->errorMessage):(object_get($resp,'errmsg').object_get($resp,'errcode'));
            throw new \Exception($errorMsg);
        }
        $this->token = $token;
        return;
    }

    public function getCard(){
        return 1;
    }

    public function createCard(){
        $url = "https://api.weixin.qq.com/card/create?access_token={$this->token}";
        $data = [
            'card'=>[
                'card_type'=>'MEMBER_CARD',
                'member_card'=>[
//                    'background_pic_url'=>'http://asics-connext.oss-cn-zhangjiakou.aliyuncs.com/default/rights/level_bg1.png',
                    'base_info'=>[
                        'logo_url'=>'http://asics-connext.oss-cn-zhangjiakou.aliyuncs.com/oss/wxlogo.jpg',
                        'brand_name'=>'测试会员卡20190103',
                        'code_type'=>'CODE_TYPE_ONLY_QRCODE',
                        'title'=>'微信会员卡20190103',
                        'color'=>'Color010',
                        'notice'=>'测试提醒',
                        'service_phone'=>'400-821-0893',
                        'description'=>'卡券使用说明测试描述',
                        'date_info'=>[
                            'type'=>'DATE_TYPE_PERMANENT',
                        ],
                        'sku'=>[
                            'quantity'=>4
                        ],
                        'get_limit'=>1,
                        'use_custom_code'=>true,
                        'can_give_friend'=>false,
                    ],
                    'supply_bonus'=>true,
                    'supply_balance'=>false,
                    'prerogative'=>'test_prerogative',
                    'auto_activate'=>false,
                    'activate_url'=>(url('wechat/active')),
                ],
            ],
        ];
        call_user_func([$this->curl,'post'],$url,json_encode($data,320));
        $resp = $this->curl->response;
        if(!empty($resp) && !object_get($resp,'errcode')){
            $card_id = object_get($resp,'card_id');
        }else{
            $errorMsg = empty($resp)?($this->curl->errorMessage):(object_get($resp,'errmsg').object_get($resp,'errcode'));
            throw new \Exception($errorMsg);
        }
        if(!empty($card_id)){
            return $card_id;
        }throw new \Exception('创建会员卡失败');
    }

    public function createQrCode($card_id,$member_code){
        $url = "https://api.weixin.qq.com/card/qrcode/create?access_token={$this->token}";
        $data = [
            'action_name'=> 'QR_CARD',
            'expire_seconds'=> 1800,
            'action_info'=> [
                'card'=>[
                    'card_id'=>$card_id,
                    'code'=>$member_code,
                    'outer_str'=>'test'
                ]
            ],
        ];
        call_user_func([$this->curl,'post'],$url,json_encode($data,320));
        $resp = $this->curl->response;
        if(!empty($resp) && !object_get($resp,'errcode')){
            //TODO
            return object_get($resp,'show_qrcode_url');
        }else{
            $errorMsg = empty($resp)?($this->curl->errorMessage):(object_get($resp,'errmsg').object_get($resp,'errcode'));
            throw new \Exception($errorMsg);
        }
    }
}
