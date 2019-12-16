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
        $method = end($requestUri);
        $strpos = strripos($method,"?");
        if($strpos!==false){
            $method = substr($method,0,$strpos);
        }
        $RequestData = request()->all();
        $RespondData = $data;
        $log = compact('RequestData','RespondData');
        //系统异常记录到日志 不需要显示给前台
        $log['error']=isset($this->error)?$this->error:'';
        log_json('api',$method,json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        return response()->json($data);
    }

    public function success($data=[], $msg = '成功'){
        return $this->respond(['code'=>1,'msg'=>$msg,'data'=>$data]);
    }

    public function error($msg = '网络异常'){
        if($msg instanceof \Exception || $msg instanceof QueryException){
            $this->error = $msg->getMessage();
            //code非1 属于系统抛出的异常 将此异常放入日志 不需要给前台
            $msg = (config('app.name')||$msg->getCode()==1)?$msg->getMessage():'网络异常';
        }
        return $this->respond(['code'=>0,'msg'=>$msg]);
    }

    /**
     * 验证错误
     * @param string $msg
     * @return mixed
     */
    public function fail($msg = '失败'){
        return $this->respond(['code'=>0,'msg'=>$msg]);
    }

}