<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends ApiController
{
    public function selectProjectByReward(Request $request){
        try{
            $json = $request->get('json');
            if($json){
                $data = json_decode($json,true);
                $model = DB::table('app_project');
                $startReward = array_get($data,'startReward');
                if($startReward){
                    $model = $model->where('reward','>',intval($startReward));
                }
                $endReward = array_get($data,'endReward');
                if($endReward){
                    $model = $model->where('reward','<=',intval($endReward));
                }
                $data = $model
                    ->orderByDesc('created_at')
                    ->orderByDesc('reward')
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function getGeneralInfo(Request $request){
        $data = [
            ['count'=>$data = DB::table('app_user')->count()],
            ['count'=>$data = DB::table('app_project')->count()],
            ['count'=>$data = DB::table('app_project')->sum('reward')],
        ];
        return response()->json($data);
    }

    public function deleteProjectById(Request $request){
        try{
            $id = $request->get('id');
            if($id){
                if(DB::table('app_project')->where('id',$id)->delete()){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function updateStatusByProjectId(Request $request){
        try{
            $id = $request->get('id');
            $status = $request->get('status');
            if($id and $status){
                if(DB::table('app_project')->where('id',$id)->update(['status'=>$status])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function updateProjectById(Request $request){
        try{
            $id = $request->get('id');
            if($id){
                if(DB::table('app_project')->where('id',$id)->limit(1)
                    ->update([
                        'contactperson'=>$request->get('contactperson'),
                        'contactphone'=>$request->get('contactphone'),
                        'department'=>$request->get('department'),
                        'projectdescribe'=>$request->get('projectdescribe'),
//                        'reward'=>$request->get('reward'),//价格不允许修改
                    ])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function chooseUserForProject(Request $request){
        try{
            $id = $request->get('id');
            if($id){
                if(DB::table('app_project')->where('id',$id)->limit(1)
                    ->update([
                        'winbidphone'=>$request->get('submitphone'),
                    ])){
                    return '操作成功';
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return '操作失败，请重试';
        }
    }

    public function wantedPublish(Request $request){
        try{
            $id = 'D'.time().rand(100,999);
            $data = [
                'address'=>$request->get('address'),
                'contactperson'=>$request->get('contactperson'),
                'contactphone'=>$request->get('contactphone'),
                'purpose'=>$request->get('purpose'),
                'department'=>$request->get('department'),
                'projectdescribe'=>$request->get('projectdescribe'),
                'reward'=>$request->get('reward'),
                'skill'=>$request->get('skill'),
                'time'=>$request->get('time'),
                'inviternum'=>$request->get('inviternum'),
                'publishphone'=>$request->get('publishphone'),
                'id'=>$id,
                'publishtime'=>date('Y-m-d'),
                'openid'=>$request->get('openid'),
            ];
            if($data){
                if(DB::table('app_project')->insert($data)){
                    return $this->success(['id'=>$id],'发布成功');
                }
            }
            throw new \Exception('发布失败',1);
        }catch (\Exception $e){
            return $this->error($e);
        }
    }

    public function selectProjectByWinbidphone(Request $request){
        try{
            $winbidphone = $request->get('winbidphone');
            if($winbidphone){
                $data = DB::table('app_project')
                    ->where('winbidphone',$winbidphone)
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function selectProjectByPunlishphone(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $data = DB::table('app_project')
                    ->where('publishphone',$phone)
                    ->get()->toArray();
                return response()->json($data);
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function selectMySubBidedProject(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $projectIds = DB::table('app_bid')
                    ->where('submitphone',$phone)
                    ->pluck('projectid')->toArray();
                if($projectIds){
                    $data = DB::table('app_project')
                        ->whereIn('id',$projectIds)
                        ->get()->toArray();
                    return response()->json($data);
                }
            }
            throw new \Exception('error');
        }catch (\Exception $e){
            return response()->json();
        }
    }

    public function selectMyWinBid(Request $request){
        try{
            $phone = $request->get('phone');
            if($phone){
                $data = DB::table('app_project')
                    ->where([
                        ['winbidphone',$phone],
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
