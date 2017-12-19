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
            $grid->column('name', '名称');
            $grid->column('cat.name', '分类');
            $grid->column('serviceType.name', '服务类型');
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
        return Admin::form(Malfunction::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称');
            $data = Categorie::orderBy('sort', 'desc')->get();
            $selectData = [];
            $data->each(function ($data) use (&$selectData) {
                $selectData[$data->id] = $data->name;
            });
            $form->select('cat_id', '分类')->options($selectData);
            $data = ServiceType::orderBy('sort', 'desc')->get();
            $selectData = [];
            $data->each(function ($data) use (&$selectData) {
                $selectData[$data->id] = $data->name;
            });
            $form->select('service_type_id', '服务类型')->options($selectData);
            $form->textarea('desc', '描述');
            $form->radio('state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(1);
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
