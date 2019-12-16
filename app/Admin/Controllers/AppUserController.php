<?php

namespace App\Admin\Controllers;

use App\Models\AppUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AppUserController extends Controller
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
        $content->header('人员管理');
        $content->body($this->grid());

        return $content;
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
        $grid = new Grid(new AppUser);
        $grid->column('nickname', '昵称');
        $grid->column('phone', '手机');
        $grid->column('skill', '技能')->display(function($text) {
            return str_limit($text, 40, '...');
        });
        $grid->column('address', '地区');
        $grid->column('winbidnum', '完成工程数');
        $grid->column('time', '注册时间')->sortable();
        $grid->column('bail', '保证金');

        $grid->model()->orderBy('id', 'desc');

        //禁用行选择checkbox
        $grid->disableRowSelector();
        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('phone','手机');
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
        $show = new Show(AppUser::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AppUser);

        $form->display('id', '编号');
        $form->display('nickname', '昵称');
        $form->display('phone', '手机号');
        $form->display('skill', '技能');
        $form->display('address', '地址');
        $form->display('introduce', '自我介绍');
        $form->display('winbidnum', '项目完成数量');
        $form->display('time', '注册时间');
        $form->text('bail','保证金')->rules('numeric');
        $form->switch('plattuijian','季度推荐');
        $form->switch('quartertuijian','平台推荐');

        $form->tools(function (Form\Tools $tools) {
            // 去掉`删除`按钮
            $tools->disableDelete();
        });

        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });


        return $form;
    }
}
