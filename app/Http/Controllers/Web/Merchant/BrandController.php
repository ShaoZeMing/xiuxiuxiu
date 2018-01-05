<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Http\Controllers\Controller;
use App\Repositories\BrandRepositoryEloquent;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {



        return Merchant::content(function (Content $content) {
            $content->header('品牌理');
            $content->description('');
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
        return Merchant::content(function (Content $content) use ($id) {

            $content->header('品牌编辑');
            $content->description('');

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
        return Merchant::content(function (Content $content) {

            $content->header('添加品牌');
            $content->description('');

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
        return Merchant::grid(Brand::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->model()->orderBy('brand_sort');
            $grid->column('brand_name', '品牌名称');
            $grid->brand_logo('LOGO')->display(function ($name) {
                return "<img src='". config('filesystems.disks.merchant.url').'/'.$name."' width='80'>";
            });
            $grid->cats('经营品类')->display(function ($cats) {
                $cats = array_map(function ($cat) {
                    return "<span class='label label-success'>{$cat['cat_name']}</span>";
                }, $cats);
                return join('&nbsp;', $cats);
            });
            $grid->brand_desc('描述');
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('brand_name','名称');
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

        return Merchant::form(Brand::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('brand_name', '品牌名称')->rules('required');
            $parent=Brand::where('brand_parent_id',0)->where('id','!=',$form->model()->id)->get()->pluck('brand_name', 'id')->toArray();
            $parent[0]='无'; ksort($parent);
            $form->select('brand_parent_id', '父级品牌')->options($parent);
            $form->textarea('brand_desc', '品牌描述')->default('');
            $form->image('brand_logo', '品牌LOGO')->resize(200,200)->uniqueName()->removable();
            $form->multipleSelect('cats', '经营品类')->options(Categorie::all()->pluck('cat_name', 'id'));
            $form->switch('brand_state','状态')->default(1);
            $form->number('brand_sort', '排序');
        });
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|int|null|static|static[]
     */
    public function apiCats(Request $request,BrandRepositoryEloquent $brandRepository)
    {
        $q = $request->get('q');
        return  $brandRepository->find($q)->cats()->get(['categories.id', DB::raw('cat_name as text')]);
    }
}
