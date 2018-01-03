<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Product;
use App\Entities\ServiceType;
use App\Http\Controllers\Controller;
use App\Repositories\ResolventRepositoryEloquent;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return Merchant::content(function (Content $content) {
            $content->header('故障列表');
            $content->description('所有故障的列表');
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

            $content->header('编辑故障');
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
        return Merchant::content(function (Content $content) {

            $content->header('添加故障');
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
        return Merchant::grid(Malfunction::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->model()->orderBy('malfunction_sort');
            $grid->column('malfunction_name', '名称')->display(function($name) {
                return "<a href='".url('merchant/malfunctions/'.$this->id)."'>$name</a>";
            });
            $grid->column('cat.cat_name', '分类')->display(function($name) {
                return "<a href='".url('merchant/cats/'.$this->cat_id)."'>$name</a>";
            });
            $grid->products('关联产品')->display(function ($products) {
                $products = array_map(function ($product) {
                    return "<span class='label label-success'>{$product['product_name']}</span>";
                }, $products);
                return join('&nbsp;', $products);
            });
            $grid->column('serviceType.service_type_name', '服务类型');
            $grid->malfunction_desc('描述');
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('malfunction_name','名称');
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
        return Merchant::form(Malfunction::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->select('cat_id', '所属品类')->options(Categorie::all()->pluck('cat_name', 'id'))->load('products','/merchant/api/cat/products');
            $form->text('malfunction_name', '故障名称')->rules('required');
            $form->select('service_type_id', '服务类型')->options(ServiceType::all()->pluck('service_type_name', 'id'));
            $form->multipleSelect('products', '产品关联')->options(Product::all()->pluck('product_name', 'id'));
            $form->textarea('malfunction_desc', '描述')->default('');
            $form->number('malfunction_sort', '排序');
            $form->switch('malfunction_state','状态')->default(1);
            $form->hasMany('resolvents','添加解决方法',function (Form\NestedForm $form) {
                $form->text('resolvent_name','方法名称')->rules('required');
                $form->textarea('resolvent_desc','方法详解')->rules('required');
                $form->url('resolvent_url','视频地址')->default('');
            });

        });
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @param ResolventRepositoryEloquent $repositoryEloquent
     * @return mixed
     */
    public function apiResolvents(Request $request,ResolventRepositoryEloquent $repositoryEloquent){
        $q = $request->get('q');
        return  $repositoryEloquent->find($q)->products()->get(['resolvent.id', DB::raw('resolvent_name as text')]);

    }


}
