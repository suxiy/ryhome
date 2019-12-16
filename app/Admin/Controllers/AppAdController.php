<?php

namespace App\Admin\Controllers;

use App\Models\AppAd;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AppAdController extends Controller
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
            ->header('商城广告管理')
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
        $grid = new Grid(new AppAd);

        $grid->title('标题');
        $grid->ADinfo('广告信息');
        $grid->phone('电话');
        $grid->time('发布时间');

        $grid->model()->orderBy('id', 'desc');

        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('title','标题');
            $filter->like('phone','电话');
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
        $show = new Show(AppAd::findOrFail($id));

        $show->panel()->title('商城广告详情');

        $show->title('标题');
        $show->ADinfo('广告信息');
        $show->image('图片')->image();
        $show->phone('电话');
        $show->time('发布时间');

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
        $form = new Form(new AppAd);

        $form->text('ADinfo', 'ADinfo');
        $form->image('image', 'Image');
        $form->mobile('phone', 'Phone');
        $form->text('time', 'Time');
        $form->text('title', 'Title');

        return $form;
    }
}
