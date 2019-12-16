<?php

namespace App\Admin\Controllers;

use App\Models\AppProject;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AppProjectController extends Controller
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
        $content->header('项目管理');
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
        $grid = new Grid(new AppProject);

        $grid->id('编号');
        $grid->publishphone('发单电话');
        $grid->skill('技能需求');
        $grid->address('地区');
        $grid->contactperson('联系人员');
        $grid->reward('项目酬劳');
        $grid->department('项目规模');
        $grid->time('完成时间');
        $grid->projectdescribe('项目描述')->display(function($text) {
            return str_limit($text, 30, '...');
        });
        $grid->status('项目状态');

        $grid->model()->orderBy('publishtime', 'desc');

        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('id','编号');
            $filter->like('publishphone','发单电话');
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
        $show = new Show(AppProject::findOrFail($id));

        $show->id('编号');
        $show->publishphone('发单电话');
        $show->skill('技能需求');
        $show->address('地区');
        $show->contactperson('联系人员');
        $show->reward('项目酬劳');
        $show->department('项目规模');
        $show->time('完成时间');
        $show->projectdescribe('项目描述');
        $show->status('项目状态');

        $show->panel()
            ->tools(function ($tools) {
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
        $form = new Form(new AppProject);

        $form->text('purpose', 'Purpose');
        $form->text('contactperson', 'Contactperson');
        $form->text('contactphone', 'Contactphone');
        $form->text('reward', 'Reward');
        $form->text('department', 'Department');
        $form->text('time', 'Time');
        $form->text('address', 'Address');
        $form->text('skill', 'Skill');
        $form->text('projectdescribe', 'Projectdescribe');
        $form->text('inviternum', 'Inviternum');
        $form->text('status', 'Status')->default('招标中');
        $form->text('winbidphone', 'Winbidphone');
        $form->text('publishphone', 'Publishphone');
        $form->text('publishtime', 'Publishtime');

        return $form;
    }
}
