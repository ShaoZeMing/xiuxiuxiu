<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductController extends Controller
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
        return Admin::grid(Product::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->model()->orderBy('sort');
            $grid->column('name', '名称');
            $grid->column('cat.name', '分类');
            $grid->column('brand.name', '品牌');
            $grid->desc('描述');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
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
        return Admin::form(Product::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称');
            $cats = Categorie::orderBy('sort', 'desc')->get();
            $catsData = $brandData = [];
            $cats->each(function ($cat) use (&$catsData) {
                $catsData[$cat->id] = $cat->name;
            });
            $form->select('cat_id', '分类')->options($catsData);
            $brands = Brand::orderBy('sort', 'desc')->get();
            $brands->each(function ($brand) use (&$brandData) {
                $brandData[$brand->id] = $brand->name;
            });
            $form->select('brand_id', '品牌')->options($brandData);
            $form->textarea('desc', '描述');
            $form->radio('state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(0);
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
