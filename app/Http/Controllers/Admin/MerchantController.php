<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Area;
use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Merchant;
use App\Entities\Product;
use App\Entities\ServiceType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class MerchantController extends Controller
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
            $content->header('商家列表');
            $content->description('这些都是老板，好生对待');
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
            $content->header('编辑商家');
            $content->description('注意哈，有些不能修改的要注意哈');
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
        return Admin::grid(Merchant::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->merchant_name('名称');
            $grid->merchant_mobile('手机号');
            $grid->merchant_nickname('昵称');
            $grid->merchant_face()->display(function ($avatar) {
                return "<img src='{$avatar}' />";
            });
            $grid->merchant_full_address('地址')->limit(30);
            $grid->column('merchant_state','状态')->switch();
            $grid->created_at('注册时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                $filter->like('merchant_name','名称');
                $filter->like('merchant_mobile','手机');
                $filter->like('merchant_full_address','地址');
                $filter->between('created_at', '注册时间')->datetime();
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
        return Admin::form(Merchant::class, function (Form $form) {
            $form->display('id', 'id');
            $form->mobile('merchant_mobile', '电话');
            $form->text('merchant_name', '名称');
            $form->text('merchant_nickname', '昵称');
            $form->image('merchant_face', '头像');
            $form->multipleSelect('cats', '分类')->options(Categorie::all()->pluck('cat_name', 'id'));
            $form->select('merchant_province','省')->options(Area::where('parent_id',0)->get()->pluck('name', 'id'))->load('city', '/api/city');
            $form->select('merchant_city','市')->load('merchant_district', '/api/city');
            $form->select('merchant_district','区');
            $form->switch('merchant_state','状态')->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->hidden('merchant_geom');
        });
    }


}
