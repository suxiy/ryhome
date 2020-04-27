<?php namespace App\Admin\Actions\Project;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VerifyBatch extends BatchAction
{
    public $name = '批量审核';

    public function handle(Collection $collection)
    {
        try{
            DB::transaction(function () use($collection){
                $data = [];
                /** @var \App\Models\AppProjectNoChecked $model */
                foreach ($collection as $model) {
                    $data[] = array_except($model->toArray(),['project_id','is_notify','created_at']);
                    $model->delete();
                }
                DB::table('app_project')->insert($data);
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
