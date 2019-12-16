<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ADController extends Controller
{
    public function selectAllAD(Request $request){
        $data = DB::table('app_ad')
            ->orderByDesc('time')
            ->get()->toArray();
        return response()->json($data);
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
                if(DB::table('app_adpublish')->insert($data)){
                    return '发布成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '发布失败';
        }
    }
}
