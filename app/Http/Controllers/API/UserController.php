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
            $openid = $request->get('openid');
            $unionid = $request->get('unionid');
            if($phone&&$password){
                $user = DB::table('app_user')
                    ->where([
                        ['phone',$phone],
                        ['password',$password]
                    ])
                    ->first();
                //统计openid
                if($openid && $user && !$user->openid){
                    DB::table('app_user')->where('id',$user->id)->update(['openid'=>$openid,'unionid'=>$unionid]);
                }
                return $this->text($user?(json_encode([$user],320)):'');
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return $this->error('');
        }
    }

    public function loginByOpenId(Request $request){
        try{
            $openid = $request->get('openid');
            $unionid = $request->get('unionid');
            if($openid){
                $data = DB::table('app_user')
                    ->where([
                        ['openid',$openid],
                    ])
                    ->first();
                if($data){
                    if($unionid && $data && !$data->unionid){
                        DB::table('app_user')->where('id',$data->id)->update(['unionid'=>$unionid]);
                    }
                    return $this->success($data);
                }else{
                    return $this->failLogin('user not exists');
                }
            }throw new \Exception('require openid');
        }catch (\Exception $e){
            return $this->error($e);
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
                'unionid'=>$request->get('unionid'),
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
