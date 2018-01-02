<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

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
            $content->header('产品管理');
            $content->description('这都是产品');
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
            $content->header('编辑产品');
            $content->description('注意编辑');
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

            $content->header('创建产品');
            $content->description('描述');

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

            $catId = request()->get('cat_id');//搜索分类下的产品
            $where = $catId ? ['cat_id' => $catId] : [];
            $grid->model()->where($where)->orderBy('product_sort');
//            $grid->id('ID')->sortable();
            $grid->column('product_name', '产品名称')->display(function($name) {
                return "<a href='".url('admin/products/'.$this->id)."'>$name</a>";
            });
            $grid->column('product_version', '产品型号');
            $grid->column('product_size', '产品规格');
            $grid->column('cat.cat_name', '产品分类');
            $grid->column('brand.brand_name', '产品品牌');
            $grid->column('product_desc', '描述')->limit(30);
            $grid->column('product_state','状态')->switch();
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('product_name','产品名称');
                $filter->equal('brand_id','产品品牌')->select(Brand::all()->pluck('brand_name', 'id'));
                $filter->equal('cat_id','产品分类')->select(Categorie::all()->pluck('cat_name', 'id'));
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
        return Admin::form(Product::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('product_name', '产品名称')->rules('required');
            $form->text('product_version', '产品型号')->default('');
            $form->text('product_size', '产品规格')->default('');
            $form->select('brand_id', '产品品牌')->options(Brand::all()->pluck('brand_name', 'id'))->load('cat_id','/admin/api/brand/cats');
            $form->select('cat_id', '产品分类')->options(Categorie::all()->pluck('cat_name', 'id'))->load('malfunctions','/admin/api/cat/malfunctions');
            $form->multipleSelect('malfunctions', '故障类型')->options(Malfunction::all()->pluck('malfunction_name', 'id'));
            $form->textarea('product_desc', '描述');
            $form->number('product_sort', '排序')->setWidth(2);
            $form->switch('product_state','状态')->default(1);
        });
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiMalfunctions(Request $request,ProductRepository $productRepository)
    {
        $q = $request->get('q');
        return  $productRepository->find($q)->malfunctions()->get(['malfunctions.id', DB::raw('malfunctions_name as text')]);
    }

}
