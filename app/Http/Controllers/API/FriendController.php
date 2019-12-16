<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function selectfriendByPhone(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $data = DB::table('app_help')
                    ->where('phone',$phone)
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function insertfriendhelp(Request $request){
        try{
            $data = [
                'phone'=>$request->get('phone'),
                'friendlist'=>$request->get('friendlist'),
                'nickname'=>$request->get('nickname'),
            ];
            if($data){
                if(DB::table('app_help')->insert($data)){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败';
        }
    }

    public function updateUserInfo(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                if(DB::table('app_help')->where('phone',$phone)->limit(1)
                    ->update([
                        'friendlist'=>$request->get('friendlist'),
                        'num'=>$request->get('num'),
                    ])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }
}
