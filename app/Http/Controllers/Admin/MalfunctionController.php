<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Product;
use App\Entities\ServiceType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class MalfunctionController extends Controller
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
        return Admin::grid(Malfunction::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->model()->orderBy('sort');
            $grid->column('name', '名称')->display(function($name) {
                return "<a href='".url('admin/malfunctions/'.$this->id)."'>$name</a>";
            });
            $grid->column('cat.name', '分类')->display(function($name) {
                return "<a href='".url('admin/cats/'.$this->cat_id)."'>$name</a>";
            });
            $grid->column('serviceType.name', '服务类型');
            $grid->desc('描述');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('name','名称');
                $filter->equal('service_type_id','服务类型')->select(ServiceType::all()->pluck('name', 'id'));
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
        return Admin::form(Malfunction::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称');
            $form->select('cat_id', '分类')->options(Categorie::all()->pluck('name', 'id'));
            $form->select('service_type_id', '服务类型')->options(ServiceType::all()->pluck('name', 'id'));
            $form->multipleSelect('products', '适用产品')->options(Product::all()->pluck('name', 'id'));
            $form->textarea('desc', '描述');
            $form->radio('state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(1);
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }


}
