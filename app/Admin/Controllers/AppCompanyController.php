<?php

namespace App\Admin\Controllers;

use App\Models\AppCompany;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AppCompanyController extends Controller
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
            ->header('企业管理')
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
        $grid = new Grid(new AppCompany);

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

        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            //TODO
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('phone','注册电话');
            $filter->like('businessNum','营业执照号码');
        });

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
        $show = new Show(AppCompany::findOrFail($id));

        $show->panel()->title('企业详情');

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
            $tools->disableEdit();
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
        $form = new Form(new AppCompany);

        $form->text('companyName', 'CompanyName');
        $form->text('corporation', 'Corporation');
        $form->text('time', 'Time');
        $form->text('capital', 'Capital');
        $form->text('companyAddress', 'CompanyAddress');
        $form->text('addressElse', 'AddressElse');
        $form->text('companyPhone', 'CompanyPhone');
        $form->text('businessNum', 'BusinessNum');
        $form->textarea('companyInfo', 'CompanyInfo');
        $form->textarea('companyType', 'CompanyType');
        $form->mobile('phone', 'Phone');
        $form->text('elsePhone', 'ElsePhone');
        $form->textarea('bussinessimg', 'Bussinessimg');
        $form->text('resgeterTime', 'ResgeterTime');

        return $form;
    }
}
