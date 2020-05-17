<?php namespace App\Admin\Actions\Company;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\AppCompanyreview;

class Verify extends RowAction
{
    public $name = '审核';

    public function handle(Model $model)
    {
        $row = $model->toArray();
        try{
            AppCompanyreview::approved($row['id']);
        }catch (\Exception $e){
            return $this->response()->success($e->getMessage())->refresh();
        }return $this->response()->success('审核通过')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定通过?');
    }

}
