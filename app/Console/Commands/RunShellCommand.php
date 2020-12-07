<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunShellCommand extends Command
{
    protected $signature = 'run:shell {option}';

    protected $description = 'run:shell';

    public function __construct(){
        parent::__construct();
    }

    public function handle()
    {
        try{
            $option = $this->argument('option');
            $allow_options = ['send_wxs_msg','create_menu','send_notify','send_winbid_msg'];
            if(in_array($option,$allow_options)){
                call_user_func([__CLASS__,$option]);
                $this->line('All done');
            }else{
                throw new \Exception('no this options');
            }
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    protected function send_wxs_msg():void{
        $project = DB::table('app_project')->where('is_send',0)->orderBy('publishtime','ASC')->first();
        if($project){
            $users = DB::table('wxs_user')->where('follow',1)->where('subscribe',1)->get();
            if($users->count()){
                $project_json = json_encode($project);
                $wechat = app()->make(\App\Lib\WechatService::class);
                foreach($users as $user){
                    $wechat->app->template_message->send([
                        'touser' => $user->open_id,
                        'template_id' => '_5MF46h7harQD6PE6TWx3DxlBKnIZfAaBzVjmCh8nSg',
                        'url' => 'https://easywechat.org',
                        'miniprogram' => [
                            'appid' => 'wxd31aecbc4af3deb2',
                            'pagepath' => 'pages/project/projectinfo?project='.$project_json,
                        ],
                        'data' => [
                            'keyword1' => $project->address,
                            'keyword2' => $project->contactperson,
                            'keyword3' => $project->status,
                            'keyword4' => '15543789003',
                            'keyword5' => $project->projectdescribe,
                        ],
                    ]);
                }
            }
            DB::table('app_project')->where('project_id',$project->project_id)->update(['is_send'=>1]);
        }
    }

    protected function send_winbid_msg(){
        $projects = DB::table('app_project')
            ->where('is_notify_winbid',0)
            ->whereNotNull('winbidphone')
            ->where('winbidphone','<>','')
            ->orderBy('publishtime','ASC')->get();
        if($projects){
            $phones = $projects->pluck('winbidphone');
            $unionids = DB::table('app_user')->whereIn('phone',$phones)->pluck('unionid','phone');
            if($unionids){
                $open_ids = DB::table('wxs_user')->where('follow',1)->where('subscribe',1)
                    ->whereIn('union_id',$unionids)->pluck('open_id','union_id');
                foreach($projects as $project){
                    $open_id = $open_ids->get($unionids->get($project->winbidphone));
                    if($open_id){
                        $project_json = json_encode($project);
                        $wechat = app()->make(\App\Lib\WechatService::class);
                        $wechat->app->template_message->send([
                            'touser' => $open_id,
                            'template_id' => 'yiatqkZMKCr8nbAc_F_lMT0wQVxDcRmvThDqlgbLCJs',
                            'url' => 'https://easywechat.org',
                            'miniprogram' => [
                                'appid' => 'wxd31aecbc4af3deb2',
                                'pagepath' => 'pages/project/projectinfo?project='.$project_json,
                            ],
                            'data' => [
                                'first'=>'恭喜您中标！',
                                'keyword1' => $project->contactperson,
                                'keyword2' => $project->purpose,
                                'remark'=>'点击查看中标详情',
                            ],
                        ]);
                    }
                }
            }
            $project_ids = $projects->pluck('project_id');
            DB::table('app_project')->whereIn('project_id',$project_ids)->update(['is_notify_winbid'=>1]);
        }
    }

    protected function create_menu(){
        $wechat = app()->make(\App\Lib\WechatService::class);
        $buttons = [
            [
                "type" => "miniprogram",
                "name" => "发单",
                "url" => "http://",
                "appid"  => 'wxd31aecbc4af3deb2',
                "pagepath"  => 'pages/homepage/homepage',
            ],
            [
                "name"       => "进入算量",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "招投标培训",
                        "url"  => 'https://ke.qq.com/course/480738?tuin=2c3033aa',
                    ],
                    [
                        "type" => "view",
                        "name" => "人才招聘",
                        "url"  => 'http://m.eqxiu.com/s/0ExRQJQA',
                    ],
                    [
                        "type" => "view",
                        "name" => "报名中心",
                        "url"  => 'http://m.eqxiu.com/s/7FTCP4tC',
                    ],
                    [
                        "type" => "view",
                        "name" => "订阅项目",
                        "url"  => url('wechat/subscribe'),
                    ],
                ],
            ],
            [
                "type" => "miniprogram",
                "name" => "抢单",
                "url" => "http://",
                "appid"  => 'wxd31aecbc4af3deb2',
                "pagepath"  => 'pages/homepage/homepage',
            ]
        ];
        $wechat->app->menu->create($buttons);
    }

    protected function send_notify(){
        $model = DB::table('app_project_no_checked')->where('is_notify',0);
        $projects = $model->orderBy('created_at','ASC')->get();
        if($projects->count()){
            \Mail::send('emails.projectSubmitNotify',['projects'=>$projects],function($message){
                $to = ['753251210@qq.com','75806763@qq.com','196247448@qq.com','243920447@qq.com'];
                $message ->to($to)->subject('算量之家项目审核');
            });
            $model->update(['is_notify'=>1]);
        }

    }

}
