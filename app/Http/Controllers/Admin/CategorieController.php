<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Http\Controllers\Controller;
use App\Repositories\CategorieRepository;
use App\Repositories\CategorieRepositoryEloquent;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Column;


class CategorieController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
//        return Admin::content(function (Content $content) {
//
//            $content->header('分类管理');
//            $content->description('description');
//
//            $content->body($this->grid());
//        });

        return Admin::content(function (Content $content) {
            $content->header('分类管理');
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('cats'));
                    $form->select('cat_parent_id','父级')->options(Categorie::selectOptions());
                    $form->text('cat_name', '名称')->rules('required');
                    $form->textarea('cat_desc', '描述')->default('');
                    $form->image('cat_logo', 'LOGO')->resize(200,200)->uniqueName()->removable();
                    $form->multipleSelect('brands', '经营品牌')->options(Brand::all()->pluck('brand_name', 'id'));
                    $form->switch('cat_state','状态')->default(1);
//                    $form->hasMany('products','产品',function (Form\NestedForm $form) {
//                        $form->text('product_name', '产品名称')->rules('required');
//                        $form->select('brand_id', '产品品牌')->options(Brand::all()->pluck('brand_name', 'id'));
//                        $form->text('product_version', '产品型号')->default('');
//                        $form->text('product_size', '产品规格')->default('');
//                        $form->textarea('product_desc', '产品描述')->default('');
//                        $form->number('product_sort', '产品排序');
//                        $form->switch('product_state','产品状态')->default(1);
//                    });
                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
//            $content->body(Categorie::tree(function ($tree) {
//                $tree->branch(function ($branch) {
//                    $src =  $branch['cat_logo'] ;
//                    $logo = "<img src='$src' style='max-width:30px;max-height:30px' class='img'/>";
//                    return "{$branch['id']} - {$branch['cat_name']} $logo";
//                });
//            }));
        });
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        return Categorie::tree(function (Tree $tree) {
            $tree->disableCreate();
            $tree->branch(function ($branch) {
                $payload = "<img src='{$branch['cat_logo']}' width='40'>&nbsp;<strong>{$branch['cat_name']}</strong>";
                return $payload;
            });
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

            $content->header('编辑分类');
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

            $content->header('新增分类');
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
        return Admin::grid(Categorie::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('cat_name', '分类名称');
            $grid->cat_logo('LOGO')->display(function ($name) {
                return "<img src='". config('filesystems.disks.admin.url').'/'.$name."' width='80'>";
            });
            $grid->brands('关联品牌')->display(function ($datas) {
                $datas = array_map(function ($data) {
                    return "<span class='label label-success'>{$data['brand_name']}</span>";
                }, $datas);
                return join('&nbsp;', $datas);
            });
            $grid->cat_desc('描述');
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('cat_name','名称');
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
        return Admin::form(Categorie::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('cat_name', '名称')->rules('required');
            $parent=Categorie::where('cat_parent_id',0)->where('id','!=',$form->getKey)->get()->pluck('cat_name', 'id')->toArray();
            $parent[0]='无'; ksort($parent);
            $form->select('cat_parent_id', '父级')->options($parent);
            $form->textarea('cat_desc', '描述')->default('');
            $form->image('cat_logo', 'LOGO')->resize(200,200)->uniqueName()->removable();
            $form->multipleSelect('brands', '经营品牌')->options(Brand::all()->pluck('brand_name', 'id'));
            $form->number('cat_sort', '排序');
            $form->switch('cat_state','状态')->default(1);
//            $form->hasMany('products','是否为该分类添加产品',function (Form\NestedForm $form) {
//                $form->text('product_name', '产品名称')->rules('required');
//                $form->select('brand_id', '产品品牌')->options(Brand::all()->pluck('brand_name', 'id'));
//                $form->text('product_version', '产品型号')->default('');
//                $form->text('product_size', '产品规格')->default('');
//                $form->textarea('product_desc', '产品描述')->default('');
//                $form->number('product_sort', '产品排序');
//                $form->switch('product_state','产品状态')->default(1);
//            });
        });
    }



    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiCats(Request $request)
    {
        $q = $request->get('q');
        $data =  Categorie::where('cat_name', 'like', "%$q%")->get(['id', 'cat_name as text']);
        return $data;
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiMalfunctions(Request $request)
    {
        $q = $request->get('q');
        $data =  Malfunction::where('cat_id', $q)->get(['id', DB::raw('malfunction_name as text')]);
        return $data;
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiProducts(Request $request,CategorieRepositoryEloquent $categorieRepository)
    {
        $q = $request->get('q');
        return  $categorieRepository->find($q)->products()->get(['products.id', DB::raw('product_name as text')]);
    }
}
