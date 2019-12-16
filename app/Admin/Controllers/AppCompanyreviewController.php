<?php

namespace App\Admin\Controllers;

use App\Models\AppCompanyreview;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;

class AppCompanyreviewController extends Controller
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
            ->header('企业注册审核')
            ->body($this->grid());
    }

    public function approve($id,Content $content)
    {
        $result = AppCompanyreview::approved($id);
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
        $grid = new Grid(new AppCompanyreview);
        $grid->column('phone', '注册电话');
        $grid->column('companyName', '企业名称');
        $grid->column('corporation', '企业法人');
        $grid->column('time', '企业注册时间');
        $grid->column('capital', '注册资本');
        $grid->column('companyAddress', '所在地区');
        $grid->column('addressElse', '详细地址');
        $grid->column('companyPhone', '企业座机');
        $grid->column('elsePhone', '企业手机');

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
        $show = new Show(AppCompanyreview::findOrFail($id));

        $show->panel()->title('企业注册审核');

        $show->phone('注册电话');
        $show->companyName('企业名称');
        $show->corporation('企业法人');
        $show->time('企业注册时间');
        $show->capital('注册资本');
        $show->companyAddress('所在地区');
        $show->addressElse('详细地址');
        $show->companyPhone('企业座机');
        $show->elsePhone('企业手机');
        $show->businessNum('营业执照号码');
        $show->bussinessimg('营业执照照片')->image();
        $show->companyType('公司类型');
        $show->companyInfo('公司介绍');
        $show->resgeterTime('上传时间');

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
        $form = new Form(new AppCompanyreview);

        return $form;
    }

    protected function renderApprove($tools,$id)
    {
        $url = '/'.ltrim($tools->getResource(), '/').'/'.$id.'/approve';
        $listPath = '/'.ltrim($tools->getResource(), '/');
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
