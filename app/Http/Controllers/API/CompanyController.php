<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CompanyController extends ApiController
{
    public function selectCompany(Request $request){
        try{
            $companyAddress = preg_replace('/\[|\]/','',$request->get('companyAddress'));
            $companyType = preg_replace('/\[|\]/','',$request->get('companyType'));
            if(!empty($companyAddress)){
                $where[] = ['companyAddress','like',"%$companyAddress%"];
            }
            if(!empty($companyType)&&$companyType!='不限类型'){
                $where[] = ['companyType','like',"%$companyType%"];
            }
            $query = DB::table('app_company');
            if(isset($where)){
                $query = $query->where($where);
            }
            $data = $query->get()->toArray();
            return response()->json($data);
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function insertCompanyReview(Request $request){
        try{
            $data = [
                'phone'=>$request->get('phone'),
                'companyName'=>$request->get('companyName'),
                'corporation'=>$request->get('corporation'),
                'time'=>$request->get('time'),
                'capital'=>$request->get('capital'),
                'companyAddress'=>$request->get('companyAddress'),
                'addressElse'=>$request->get('addressElse'),
                'companyPhone'=>$request->get('companyPhone'),
                'businessNum'=>$request->get('businessNum'),
                'bussinessimg'=>$request->get('bussinessimg'),
                'companyInfo'=>$request->get('companyInfo'),
                'companyType'=>$request->get('companyType'),
                'elsePhone'=>$request->get('elsePhone'),
                'resgeterTime'=>date('Y-m-d'),
            ];
            log_array('api',__FUNCTION__,$data);
            if($data){
                $img = $data['bussinessimg'];
                $info = pathinfo($img);
                if(isset($info['extension'])&&in_array($info['extension'],['jpg','jpeg','png'])){
                    $phone = $data['phone'];
                    if(DB::table('app_company')->where('phone',$phone)->exists()){
                        throw new \Exception('发布失败,该手机号已注册',1);
                    }if(DB::table('app_companyreview')->where('phone',$phone)->exists()){
                        throw new \Exception('发布失败,该手机号已注册过',1);
                    }
                    if(DB::table('app_companyreview')->insert($data)){
                        return '发布成功';
                    }
                }else{
                    throw new \Exception('图片格式错误,只允许jpg,png',1);
                }
            }
            throw new \Exception('发布失败',1);
        }catch (\Exception $e){
            return $e->getCode()==1?$e->getMessage():'发布失败';
        }
    }

    public function updateUserInfo(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                if(DB::table('app_company')->where('phone',$phone)->limit(1)
                    ->update([
                        'companyName'=>$request->get('companyName'),
                        '$corporation'=>$request->get('corporation'),
                        'time'=>$request->get('time'),
                        'capital'=>$request->get('capital'),
                        'companyPhone'=>$request->get('companyPhone'),
                        'businessNum'=>$request->get('businessNum'),
                        'companyInfo'=>$request->get('companyInfo'),
                        'elsePhone'=>$request->get('elsePhone'),
                    ])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function companyLogin(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $data = DB::table('app_company')
                    ->where([
                        ['phone',$phone],
                    ])
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

}
