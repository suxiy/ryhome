<?php namespace App\Admin\Actions\Project;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Verify extends RowAction
{
    public $name = '审核';

    public function handle(Model $model)
    {
        $row = collect($this->getRow());
        $data = $row->except(['project_id','is_notify','created_at'])->toArray();
        try{
            DB::transaction(function () use($row,$data){
                if(DB::table('app_project')->insert($data)){
                    if(DB::table('app_project_no_checked')->where('project_id',$row->get('project_id'))->delete()){
                        return true;
                    }
                }throw new \Exception('审核失败');
            });
        }catch (\Exception $e){
            return $this->response()->success($e->getMessage())->refresh();
        }return $this->response()->success('审核通过')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定通过?');
    }

}
