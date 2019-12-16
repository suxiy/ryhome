<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Qcloud\Sms\SmsSingleSender;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    private $appid = 1400149146; // 1400开头
    private $appkey = "dd72b1f2e8c5dd8ffa08ec7f186aa99f";
    private $templateId = ['get_code'=>210545];
    private $smsSign = "瑞源算量之家";
    public function getCode(Request $request){
        try{
            $type = 'get_code';
            $phone = $request->get('phone');
            if($phone){
                $code = rand(100000,999999);
                $ssender = new SmsSingleSender($this->appid, $this->appkey);
                $params = [$code,5];//数组具体的元素个数和模板中变量个数必须一致，例如事例中 templateId:5678对应一个变量，参数数组中元素个数也必须是一个
                $result = $ssender->sendWithParam("86", $phone, $this->templateId[$type],
                    $params, $this->smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                $rsp = json_decode($result,true);
                if($rsp['result']==0){
                    DB::table('cus_verify_code')
                        ->updateOrInsert(['mobile'=>$phone,'type'=>$type],['mobile'=>$phone,'type'=>$type,'code'=>$code,'updated_at'=>date('Y-m-d H:i:s')]);
                    return $code;
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json([],500);
        }
    }
}
