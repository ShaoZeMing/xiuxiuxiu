<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Request;

class BrandController extends Controller
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
            $content->header('品牌管理');
            $content->description('企业自己添加，后台添加');
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
        dd($id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('品牌编辑');
            $content->description('编辑品牌');

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
        return Admin::grid(Brand::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->model()->orderBy('brand_sort');
            $grid->column('brand_name', '名称');
            $grid->column('parent.brand_name', '父级品牌');
            $grid->brand_desc('描述');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('brand_name','名称');
                $filter->between('created_at', '创建时间')->datetime();
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
        return Admin::form(Brand::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('brand_name', '名称')->rules('required');
            $form->select('brand_parent_id', '父级')->options(Brand::all()->pluck('name', 'id'))->default([0=>'IE打卡福']);
            $form->textarea('brand_desc', '描述')->rules('required');;
            $form->radio('brand_state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(1);
            $form->number('brand_sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');

        });
    }
}
