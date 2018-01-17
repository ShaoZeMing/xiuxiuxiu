<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Area;
use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Merchant;
use App\Entities\ServiceType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Shaozeming\LumenPostgis\Geometries\GeomPoint;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;

//use ShaoZeMing\Merchant\Facades\Merchant;


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
        return \ShaoZeMing\Merchant\Facades\Merchant::content(function (Content $content) {
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
        return \ShaoZeMing\Merchant\Facades\Merchant::content(function (Content $content) use ($id) {
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
        return \ShaoZeMing\Merchant\Facades\Merchant::content(function (Content $content) {

            $content->header('注册商家');
            $content->description('注册这些商家');
            $content->body($this->form());
        });
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * 商家添加品类
     */
    public function cats($id, Request $request)
    {
        try {
            $cats = $request->get('cats');
            Log::info('添加ID', [$id, $cats]);
            $cats = array_filter($cats);
            $cats = Categorie::getAddIds($cats);
            Log::info('整理ID', [$id, $cats]);
            Merchant::find($id)->cats()->syncWithoutDetaching($cats);
            return redirect(merchant_base_path('cats'))->with(['message' => '添加成功']);

        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * 商家添加品类
     */
    public function brands($id, Request $request)
    {
        try {
            $brands = $request->get('brands');
            Log::info('添加ID', [$id, $brands]);
            $brands = array_filter($brands);
            $brands = Brand::getAddIds($brands);
            Log::info('整理ID', [$id, $brands]);
            Merchant::find($id)->brands()->syncWithoutDetaching($brands);
            return redirect(merchant_base_path('brands'))->with(['message' => '添加成功']);

        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

    }
    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * 商家添加品类
     */
    public function serviceTypes($id, Request $request)
    {
        try {
            $serviceTypes = $request->get('service_types');
            Log::info('添加ID', [$id, $serviceTypes]);
            $serviceTypes = array_filter($serviceTypes);
            Log::info('整理ID', [$id, $serviceTypes]);
            Merchant::find($id)->serviceTypes()->syncWithoutDetaching($serviceTypes);
            return redirect(merchant_base_path('service_types'))->with(['message' => '添加成功']);

        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

    }
    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $id
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * 商家添加品类
     */
    public function malfunctions($id, Request $request)
    {
        try {
            $malfunctions = $request->get('malfunctions');
            Log::info('添加ID', [$id, $malfunctions]);
            $malfunctions = array_filter($malfunctions);
            Log::info('整理ID', [$id, $malfunctions]);
            Merchant::find($id)->malfunctions()->syncWithoutDetaching($malfunctions);
            return redirect(merchant_base_path('malfunctions'))->with(['message' => '添加成功']);

        } catch (\Exception $e) {
            Log::error($e, [__METHOD__]);
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return \ShaoZeMing\Merchant\Facades\Merchant::grid(Merchant::class, function (Grid $grid) {

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

        return \ShaoZeMing\Merchant\Facades\Merchant::form(Merchant::class, function (Form $form) {
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
                $form->merchant_province = Area::find($form->merchant_province_id)->name;
                $form->merchant_city = Area::find($form->merchant_city_id)->name;
                $form->merchant_district = Area::find($form->merchant_district_id)->name;
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
