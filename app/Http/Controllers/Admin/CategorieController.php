<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Categorie;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CategorieController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Categorie::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
//            dump($grid->model()->id);
            $grid->column('name','名称');
            $grid->column('parent.name','父级分类');
            $grid->desc('描述');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {
                // 设置created_at字段的范围查询
                $filter->between('created_at', 'Created Time')->datetime();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Categorie::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称');
            $cats = Categorie::where(['parent_id'=>0])->get();
           $catData['0'] = '无';
            $cats->each(function ($cat)use(&$catData){
                 $catData[$cat->id] =  $cat->name;
            });
//            dd($catData);
            $form->select('parent_id', '父级')->options($catData);
            $form->textarea('desc', '描述');
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');

        });
    }
}
