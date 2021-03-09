<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ADController extends ApiController
{
    public function selectAllAD(Request $request){
        $data = DB::table('app_ad')
            ->orderByDesc('time')
            ->get()->toArray();
        return response()->json($data);
    }

    public function AdGet(Request $request){
        $id = $request->get('id');
        $data = DB::table('app_ad')->find($id);
        return $this->success($data);
    }

    public function ADpublish(Request $request){
        try{
            $data = [
                'phone'=>$request->get('phone'),
                'title'=>$request->get('title'),
                'ADinfo'=>$request->get('ADinfo'),
                'image'=>$request->get('image'),
                'time'=>date('Y-m-d H:i:s'),
            ];
            if($data){
                $img = $data['image'];
                $info = pathinfo($img);
                if(isset($info['extension'])&&in_array($info['extension'],['jpg','jpeg','png'])){
                    if(DB::table('app_adpublish')->insert($data)){
                        return '发布成功';
                    }
                }else{
                    throw new \Exception('图片格式错误,只允许jpg,png',1);
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return $e->getCode()==1?$e->getMessage():'发布失败';
        }
    }
}
