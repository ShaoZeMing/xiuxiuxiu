<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

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

            $content->header('分类管理');
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

            $content->header('编辑分类');
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

            $content->header('新增分类');
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
            $grid->column('cat_name', '名称');
            $grid->column('parent.cat_name', '父级分类');
            $grid->cat_desc('描述');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('cat_name','名称');
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
        return Admin::form(Categorie::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('cat_name', '名称')->rules('required');
            $parent=Categorie::where('cat_parent_id',0)->get()->pluck('cat_name', 'id')->toArray();
            $parent[0]='无'; ksort($parent);
            $form->select('cat_parent_id', '父级')->options($parent);
            $form->textarea('cat_desc', '描述')->default('z');
            $form->switch('cat_state','状态')->default(1);
            $form->number('cat_sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->hasMany('products','是否为该分类添加产品',function (Form\NestedForm $form) {
                $form->text('product_name', '产品名称')->rules('required');
                $form->select('brand_id', '产品品牌')->options(Brand::all()->pluck('brand_name', 'id'));
                $form->text('product_version', '产品型号')->default('');
                $form->text('product_size', '产品规格')->default('');
                $form->textarea('product_desc', '产品描述')->default('');
               $form->switch('product_state','产品状态')->default(1);
                $form->number('product_sort', '产品排序');
            });

        });
    }



    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiSearch(Request $request)
    {
        $q = $request->get('q');
        $data =  Categorie::where('cat_name', 'like', "%$q%")->get(['id', 'cat_name as text']);
        return $data;
    }
}
