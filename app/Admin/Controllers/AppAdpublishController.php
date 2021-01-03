<?php

namespace App\Admin\Controllers;

use App\Models\AppAdpublish;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;

class AppAdpublishController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商城广告审核')
            ->body($this->grid());
    }

    public function approve($id,Content $content)
    {
        $result = AppAdpublish::approved($id);
        if ($result) {
            $data = [
                'status'  => true,
                'message' => '操作成功',
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => '操作失败',
            ];
        }
        return response()->json($data);
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AppAdpublish);

        $grid->column('title', '标题');
        $grid->column('ADinfo', '广告信息');
        $grid->column('phone', '发布人账号');
        $grid->column('time', '发布时间');

        $grid->model()->orderBy('id', 'desc');

        //禁用行选择checkbox
        $grid->disableRowSelector();
        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableFilter();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(AppAdpublish::findOrFail($id));

        $show->panel()->title('商城广告审核');

        $show->title('标题');
        $show->ADinfo('广告信息');
        $show->image('图片')->image();
        $show->phone('发布人账号');
        $show->time('发布时间');

        $show->panel()->tools(function($tools)use($id){
            $tools->disableList();
            $tools->disableEdit();

            $tools->append($this->renderApprove($tools,$id));
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AppAdpublish);

        return $form;
    }

    protected function renderApprove($tools,$id)
    {
        $url = ltrim($tools->getResource(), '/').'/'.$id.'/approve';
        $listPath = ltrim($tools->getResource(), '/');
        $approveConfirm = '确认审核通过';
        $confirm = '确认';
        $cancel = trans('admin.cancel');
        $class = uniqid();
        $script = <<<SCRIPT
$('.{$class}-approve').unbind('click').click(function() {
    swal({
        title: "$approveConfirm",
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "$confirm",
        showLoaderOnConfirm: true,
        cancelButtonText: "$cancel",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'POST',
                    url: "{$url}",
                    data: {
                        _token:LA.token,
                    },
                    success: function (data) {
                    console.log(data)
                        if(data.status){
                            $.pjax({container:'#pjax-container', url: '{$listPath}' });
                        }
                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;

        Admin::script($script);

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="" class="btn btn-sm btn-success {$class}-approve" title="通过">
        <i class="fa fa-edit"></i><span class="hidden-xs"> 通过</span>
    </a>
</div>
HTML;
    }
}
