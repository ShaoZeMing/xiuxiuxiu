<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Area;
use App\Entities\Categorie;
use App\Entities\Order;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ExampleController extends Controller
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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');
            $area = Area::where('parent_id',0)->get();
            $province=[];
            $area->each(function ($provinces)use(&$province){
                $province[$provinces->id] = $provinces->name;
            });
            $form->hasMany('sub','城市', function (Form\NestedForm $form) {
                $form->text('name');
                $form->image('parent_id');
                $form->datetime('completed_at');
            });
            $form->select('province')->options($province)->load('city', '/api/city');
            $form->select('city');
            $form->color('color')->default('#ccc');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }




}
