<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function submitbid(Request $request){
        try{
            $data = [
                'submitphone'=>$request->get('submitphone'),
                'projectid'=>$request->get('projectid'),
                'submitperson'=>$request->get('submitperson'),
                'submitbidmoney'=>$request->get('sumbmitbidmoney'),
                'submitbiddescribe'=>$request->get('submitbiddescribe'),
            ];
            if($data){
                DB::table('app_bid')->insert($data);
                return '操作成功';
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败';
        }
    }

    public function selectBidInfo(Request $request){
        try{
            $projectid = $request->get('projectid');
            $submitphone = $request->get('submitphone');
            if($projectid and $submitphone){
                $data = DB::table('app_bid')
                    ->where([
                        ['projectid',$projectid],
                        ['submitphone',$submitphone]
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
