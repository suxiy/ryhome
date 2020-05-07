<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    public function selectAllUser(Request $request){
        $data = DB::table('app_user')
            ->get()->toArray();
        return response()->json($data);
    }

    public function selectUserByPhone(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $data = DB::table('app_user')
                    ->where('phone',$phone)
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function login(Request $request){
        try{
            $phone = $request->get('phone');
            $password = $request->get('password');
            if($phone&&$password){
                $data = DB::table('app_user')
                    ->where([
                        ['phone',$phone],
                        ['password',$password]
                    ])
                    ->get(['id','nickname','phone','skill','address','introduce','time'])->toArray();
                return $this->success($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return $this->error();
        }
    }

    public function selectUserByProjectId(Request $request){
        try{
            $projectid = $request->get('projectid');
            if($projectid){
                $data = DB::table('app_user')
                    ->join('app_bid', 'app_bid.submitphone', '=', 'app_user.phone')
                    ->where('app_bid.projectid',$projectid)
                    ->select('app_user.*', 'app_bid.submitbidmoney')
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function selectUser(Request $request){
        try{
            $address = $request->get('address');
            $skill = $request->get('skill');
            if($address and $skill){
                $data = DB::table('app_user')
                    ->where([
                        ['address','like',"%$address%"],
                        ['skill','like',"%$skill%"]
                    ])
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function updateUserInfo(Request $request){
        try{
            $nickname = $request->get('nickname');
            $phone = $request->get('phone');
            $skill = $request->get('skill');
            $introduce = $request->get('introduce');
            if($phone){
                if(DB::table('app_user')->where('phone',$phone)->limit(1)
                    ->update(['nickname'=>$nickname,'skill'=>$skill,'introduce'=>$introduce])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function insertUser(Request $request){
        try{
            $data = [
                'address'=>$request->get('address'),
                'nickname'=>$request->get('nickname'),
                'password'=>$request->get('password'),
                'phone'=>$request->get('phone'),
                'skill'=>$request->get('skill'),
                'introduce'=>$request->get('introduce'),
                'time'=>date('Y-m-d H:i:s'),
                'openid'=>$request->get('openid'),
                'inviter'=>$request->get('invite_code'),
            ];
            if($data){
                if(DB::table('app_user')->insert($data)){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function updatePassword(Request $request){
        try{
            $phone = $request->get('phone');
            $password = $request->get('password');
            if($phone and $password){
                if(DB::table('app_user')->where('phone',$phone)->limit(1)
                    ->update(['password'=>$password])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }



}
