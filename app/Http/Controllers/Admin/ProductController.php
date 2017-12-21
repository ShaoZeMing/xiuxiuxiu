<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Categorie;
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

            $catId = request()->get('cat_id');//搜索分类下的产品
            $where = $catId ? ['cat_id' => $catId] : [];

            $grid->id('ID')->sortable();
            $grid->model()->where($where)->orderBy('sort');
            $grid->column('name', '名称')->display(function($name) {
                return "<a href='".url('admin/products/'.$this->id)."'>$name</a>";
            });
            $grid->column('cat.name', '分类');
            $grid->desc('描述')->limit(30);
            $grid->column('state','状态')->switch();
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->actions(function ($actions) {
                $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
                // prepend一个操作
                $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');

            });

            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                $filter->like('name', '产品名称');
//                $filter->equal('name','分类')->select('api/cats');
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
            $form->text('name', '名称');
            $data = Categorie::orderBy('sort', 'desc')->get();
            $selectData = [];
            $data->each(function ($data) use (&$selectData) {
                $selectData[$data->id] = $data->name;
            });
            $form->select('cat_id', '分类')->options($selectData);
            $form->textarea('desc', '描述');
            $form->switch('state','状态');
            $form->number('sort', '排序');
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

        return Product::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

}
