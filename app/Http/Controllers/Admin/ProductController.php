<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Product;
use App\Http\Controllers\Controller;
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
            $grid->id('ID')->sortable();
            $grid->column('product_name', '名称')->display(function($name) {
                return "<a href='".url('admin/products/'.$this->id)."'>$name</a>";
            });
            $grid->column('cat.name', '分类');
            $grid->product_desc('描述')->limit(30);
            $grid->column('product_state','状态')->switch();
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('product_name','名称');
                $filter->equal('cat_id','分类')->select(Categorie::all()->pluck('name', 'id'));
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
            $form->text('product_name', '名称');
            $form->select('cat_id', '分类')->options(Categorie::all()->pluck('cat_name', 'id'));
            $form->multipleSelect('malfunctions', '故障类型')->options(Malfunction::all()->pluck('malfunction_name', 'id'));
            $form->textarea('product_desc', '描述');
            $form->switch('product_state','状态')->default(1);
            $form->number('product_sort', '排序')->setWidth(2);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
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
        $data = Product::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
        return $data;
    }

}
