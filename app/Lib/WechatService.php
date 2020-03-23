<?php namespace App\Lib;

use EasyWeChat\Factory;
use App\Models\WxsUser;

class WechatService
{
    /**
     * @var \EasyWeChat\OfficialAccount\Application
     */
    public $app;
    public function __construct()
    {
        $config = [
            'app_id' => 'wxce7d82272183be5b',
            'secret' => '0e5bfc1aacf97d1e26bd05693f5beeae',
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'token' => 'lilei1230',
            'aes_key' => 'aO4cX0JgyE0uomilsqkQbfAyLCjgdP5TYW3R68Vo9NC',
            'response_type' => 'array',
            'oauth' => [
                'scopes'   => ['snsapi_base'],
                'callback' => '/oauth_callback',
            ],
        ];
        $this->app = Factory::officialAccount($config);
        $this->app['cache'] = new \Overtrue\LaravelWeChat\CacheBridge(app('cache.store'));
    }

    public function serve(){
        $this->app->server->push(function ($message) {
            log_array('wechat','message',$message);
            $event = array_get($message,'Event');
            switch ($message['MsgType']) {
                case 'event':
                    if ($event == 'subscribe') {
                        //关注
####################################################################################
                        WxsUser::query()->updateOrInsert(['open_id' => $message['FromUserName']], ['follow' => 1]);
                        return "本平台包含以下服务：工程管理服务、工程造价咨询服务、可研报告、投资估算制作、招标代理、招标标底及投标报价的编制、审核；工程资料、工程概算、预算；竣工结算、决算咨询信息服务；
联系方式：0431-81090840
                     0431-80818709
微信客服：15543789003

订阅项目信息请点击<a href='".url('wechat/subscribe')."'>此处</a>!";
####################################################################################
                    } elseif ($event == 'unsubscribe') {
                        //取消关注
                        WxsUser::query()->updateOrInsert(['open_id' => $message['FromUserName']], ['follow' => 0]);
                    }
                    break;
//                case 'text':
//
//                    break;
            }
            return '订阅项目信息请点击<a href="'.url('wechat/subscribe').'">此处</a>!';
        });
        return $this->app->server->serve();
    }

}
