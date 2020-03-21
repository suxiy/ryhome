<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Http\Helpers\Api\ApiResponse;
use App\Models\WxsUser;

class WeChatServiceController extends BaseController
{
    use ApiResponse;
    /**
     * @var \App\Lib\WechatService
     */
    public $wechat;
    public function __construct(){
        $this->wechat = app()->make(\App\Lib\WechatService::class);
    }

    public function test(Request $request){
        return 123;
    }

    public function serve(Request $request){
        $resp = $this->wechat->serve();
        return $this->text($resp);
    }

    public function subscribe(Request $request){
        $user = session()->get('wechat_user');
        if(empty($user)){
            $oauth = $this->wechat->app->oauth;
            session()->put('target_url',$request->fullUrl());
            session()->save();
            return $oauth->redirect();
        }
        $open_id = array_get($user,'original.openid');
        $is_subscribe = WxsUser::query()->where(['open_id' => $open_id])->value('subscribe');
        return view("front.wechat.subscribe",['is_subscribe'=>$is_subscribe,'url'=>url('wechat/subscribe_callback')]);
    }

    public function subscribe_callback(Request $request){
        $user = session()->get('wechat_user');
        if(empty($user)){
            $oauth = $this->wechat->app->oauth;
            session()->put('target_url',$request->fullUrl());
            session()->save();
            return $oauth->redirect();
        }
        $open_id = array_get($user,'original.openid');
        $user_info = $this->wechat->app->user->get($open_id);
        $union_id = array_get($user_info,'unionid');
        $subscribe = $request->get('cancel')?0:1;
        WxsUser::query()->updateOrInsert(['open_id' => $open_id], ['union_id'=>$union_id,'subscribe' => $subscribe]);
        header('Location: '.url('wechat/subscribe'));
    }

    public function oauth_callback(Request $request){
        $oauth = $this->wechat->app->oauth;
        $user = $oauth->user();
        session()->put('wechat_user',$user->toArray());
        session()->save();
        $target_url = session()->get('target_url')?:'/';
        header('location:'. $target_url);
    }

}
