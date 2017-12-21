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
            $grid->model()->orderBy('sort');
            $grid->column('name', '名称');
            $grid->column('parent.name', '父级品牌');
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
        return Admin::form(Brand::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称');
            $cats = Categorie::where(['parent_id' => 0])->orderBy('sort', 'desc')->get();
            $catsData = $brandData = [];
            $cats->each(function ($cat) use (&$catsData) {
                $catsData[$cat->id] = $cat->name;
            });
            $form->select('cat_id', '分类')->options($catsData);
            $brands = Brand::where(['parent_id' => 0])->orderBy('sort', 'desc')->get();
            $brands->each(function ($brand) use (&$brandData) {
                $brandData[$brand->id] = $brand->name;
            });
            $form->select('parent_id', '父级')->options($brandData);
            $form->textarea('desc', '描述');
            $form->radio('state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(1);
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');

        });
    }
}
