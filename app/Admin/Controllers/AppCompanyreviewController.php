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
            $actions->add(new \App\Admin\Actions\Company\Verify);
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


}
