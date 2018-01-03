<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Area;
use App\Entities\Categorie;
use App\Entities\Merchant;
use App\Http\Controllers\Controller;
use ShaoZeMing\Merchant\Controllers\ModelForm;
//use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shaozeming\LumenPostgis\Geometries\GeomPoint;


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

            $content->header('注册商家');
            $content->description('注册这些商家');
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

            $grid->column('id', 'ID')->sortable();
            $grid->column('merchant_name', '名称');
            $grid->column('merchant_mobile', '手机号');
            $grid->column('merchant_face', '头像')->display(function ($avatar) {
                return "<img src='{$avatar}' />";
            });
            $grid->column('merchant_full_address', '地址')->limit(30);
            $grid->column('merchant_state', '状态')->switch();
            $grid->created_at('注册时间');
            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                $filter->like('merchant_name', '名称');
                $filter->like('merchant_mobile', '手机');
                $filter->like('merchant_full_address', '地址');
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
            $form->image('merchant_face', '头像')->resize(200, 200)->uniqueName()->removable();;
            $form->multipleSelect('cats', '分类')->options(Categorie::all()->pluck('cat_name', 'id'));
            $form->select('merchant_province_id', '省')->options(Area::where('parent_id', 0)->orderBy('id')->get()->pluck('name', 'id'))->load('merchant_city_id', '/api/city');
            $form->select('merchant_city_id', '市')->load('merchant_district_id', '/api/city');
            $form->select('merchant_district_id', '区');
            $form->switch('merchant_state', '状态')->default(1);
            $form->text('merchant_address', '地址');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');


            $form->hidden('merchant_province');
            $form->hidden('merchant_city');
            $form->hidden('merchant_district');
            $form->hidden('merchant_full_address');
            $form->hidden('merchant_lng');
            $form->hidden('merchant_lat');
            $form->hidden('merchant_geom');
            $form->saving(function (Form $form) {
                $form->merchant_province= Area::find($form->merchant_province_id)->name;
                $form->merchant_city= Area::find($form->merchant_city_id)->name;
                $form->merchant_district= Area::find($form->merchant_district_id)->name;
                $form->merchant_full_address = $form->merchant_province . $form->merchant_city . $form->merchant_district . $form->merchant_address;
                list($lng, $lat) = retry(2, function () use ($form) {
                    $geo = app('amap')->getLocation($form->merchant_full_address, $form->merchant_province . $form->merchant_city . $form->merchant_district);
                    Log::info('获取商家经纬度api', [$geo, __METHOD__]);
                    $location = $geo->location;
                    return explode(',', $location);
                });
                $form->merchant_lng = $lng;
                $form->merchant_lat = $lat;
                $form->merchant_geom = new GeomPoint($lng, $lat);
            });
        });
    }


//
//    public function store()
//    {
//
//        return $this->form()->store();
//
//    }

}
