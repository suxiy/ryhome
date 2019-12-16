<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class WechatController extends ApiController
{
    public function code2Session(Request $request){
        try{
            $result = app()->make(\App\Lib\Wechat::class)->code2Session($request->get('code'));
            if(empty($result['errcode'])){
                return $this->success($result);
            }
            throw new \Exception($result['errmsg'],1);
        }catch (\Exception $e){
            return $this->error($e);
        }
    }

    public function unifiedOrder(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'openid' => 'required',
                'order_id' => 'required',
            ],[
                'required' => ':attribute为必填项',
            ]);
            if ($validator->fails())return $this->fail($validator->errors()->first());

            $openid = $request->get('openid');
            $order_id = $request->get('order_id');
            $result = app()->make(\App\Lib\Wechat::class)->unifiedOrder($openid,$order_id);
            if($result){
                return $this->success($result);
            }
            throw new \Exception($result['return_msg']);
        }catch (\Exception $e){
            return $this->error($e);
        }
    }

    public function wechatNotify(Request $request){
        $response = app()->make(\App\Lib\Wechat::class)->notify();
        $log['ResponseData'] = $response;
        log_json('api','wechatNotify',json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        return $response;
    }

}
