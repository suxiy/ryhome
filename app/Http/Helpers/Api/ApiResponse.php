<?php

namespace App\Http\Helpers\Api;

use \Illuminate\Database\QueryException;

trait ApiResponse
{
    //记录日志中的异常
    protected $error;
    /**
     * @param $data
     * @return mixed
     */
    public function respond($data){
        //返回时统一记录接口入参出参日志
        $requestUri = explode('/',request()->getRequestUri());
        $dir = 'api';
        if(in_array('spi',$requestUri)){
            $dir = 'spi';
        }
        $method = implode('-',array_slice($requestUri,array_search($dir,$requestUri)+1,count($requestUri)-1));
        $strpos = strripos($method,"?");
        if($strpos!==false){
            $method = substr($method,0,$strpos);
        }
        $RequestData = request()->all();
        $RespondData = $data;
        $log = compact('RequestData','RespondData');
        //系统异常记录到日志 不需要显示给前台
        $log['error']=$this->error??'';
        log_array($dir,$method,$log);
        return response()->json($data);
    }

    public function success($data=[], $msg = '成功'){
        return $this->respond(['code'=>1,'message'=>$msg,'data'=>$data]);
    }

    /**
     * 致命错误
     * @param $exception
     * @param array $data(强制返回的格式)
     * @return mixed
     */
    public function error($exception,$data = []){
        if($exception instanceof \Exception || $exception instanceof QueryException){
            $this->error = $exception->getMessage().'--file:'.$exception->getFile().'--line:'.$exception->getLine();
        }
        if($exception->getCode()==1){
            $msg = $exception->getMessage();
        }else{
            $msg = config('app.debug')?$exception->getMessage():'网络异常';
        }
        $data = $data?:['code'=>0,'message'=>$msg];
        return $this->respond($data);
    }

    /**
     * 验证错误
     * @param string $msg
     * @return mixed
     */
    public function fail($msg = '网络异常'){
        return $this->respond(['code'=>0,'message'=>$msg]);
    }

    /**
     * 跳注册页面
     * @return mixed
     */
    public function failLogin(){
        return $this->respond([
            'code'=>2,
            'message'=>'',
            'data'=>[
                'isConfirm'=>false,
                'name'=>'',
                'phone'=>''
            ]
        ]);
    }

    /**
     * 跳指定地址
     * @param $url
     * @return mixed
     */
    public function redirectLogin($url){
        return $this->respond([
            'code'=>3,
            'message'=>'',
            'data'=>[
                'isConfirm'=>false,
                'name'=>'',
                'phone'=>'',
                'loginUrl'=>$url,
            ]
        ]);
    }

    /**
     * 弹框显示获赠优惠券
     * @param $coupon
     * @return mixed
     */
    public function receivedCoupon($coupon){
        return $this->respond([
            'code'=>4,
            'message'=>'',
            'data'=>[
                'isConfirm'=>false,
                'name'=>'',
                'phone'=>'',
                'coupon'=>$coupon,
            ]
        ]);
    }

    /**
     * 弹框提示
     * @param $msg
     * @return mixed
     */
    public function alert($msg){
        return $this->respond([
            'code'=>5,
            'message'=>$msg,
            'data'=>[]
        ]);
    }
}