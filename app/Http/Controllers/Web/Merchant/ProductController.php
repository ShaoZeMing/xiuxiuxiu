<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\BrandM;
use App\Entities\CategorieM;
use App\Entities\Malfunction;
use App\Entities\MerchantProduct;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryEloquent;
use Illuminate\Support\Facades\Log;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ModelForm;
    public static $ids = [];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Merchant::content(function (Content $content) {
            $content->header('产品管理');
            $content->description('这都是产品');
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
            $content->header('编辑产品');
            $content->description('编辑需谨慎');
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

            $content->header('创建产品');
            $content->description('创建你自己的产品');

            $content->body($this->form());
        });
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Log::info('删除产品',[$id,__METHOD__]);
            $merchant = getMerchantInfo();
            $res = $merchant->products()->detach($id);
            if ($res) {
                return response()->json([
                    'status'  => true,
                    'message' => '删除成功',
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => "删除失败",
                ]);
            }
        }catch (\Exception $e){
            Log::error($e,[__METHOD__]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Merchant::grid(Product::class, function (Grid $grid) {

            $catId = request()->get('cat_id');//搜索分类下的产品
            $where = $catId ? ['cat_id' => $catId] : [];
            $ids  = MerchantProduct::where('merchant_id',getMerchantId())->get(['product_id'])->toArray();
            self::$ids = array_column($ids, 'product_id');
            $grid->model()->where($where)->whereIn('id', self::$ids)->orderBy('product_sort');
            $grid->column('product_name', '产品名称');
            $grid->column('product_version', '产品型号');
            $grid->column('product_size', '产品规格');
            $grid->column('cat.cat_name', '产品分类');
            $grid->column('brand.brand_name', '产品品牌');
            $grid->column('created_at', '创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('product_name','产品名称');
                $filter->equal('brand_id','产品品牌')->select(BrandM::selectMerchantOptions());
                $filter->equal('cat_id','产品分类')->select(CategorieM::selectMerchantOptions());
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
        return Merchant::form(Product::class, function (Form $form) {
            $form->text('product_name', '产品名称')->rules('required');
            $form->text('product_version', '产品型号')->default('&nbsp;');
            $form->text('product_size', '产品规格')->default('&nbsp;');
            $form->select('brand_id', '产品品牌')->options(BrandM::selectMerchantOptions('—'));
            $form->select('cat_id', '产品分类')->options(CategorieM::selectMerchantOptions('—'))->load('malfunctions','/merchant/api/cat/malfunctions');
            $form->multipleSelect('malfunctions', '故障类型')->options(getMerchantInfo()->malfunctions()->get()->pluck('malfunction_name', 'id'));
            $form->textarea('product_desc', '产品描述');
            $form->saved(function (Form $form){
                getMerchantInfo()->products()->syncWithoutDetaching($form->model()->id);
            });
        });
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiMalfunctions(Request $request,ProductRepositoryEloquent $productRepository)
    {
        $q = $request->get('q');
        if(!$q){
            return [];
        }
        return  $productRepository->find($q)->malfunctions()->get(['id', DB::raw('malfunction_name as text')]);
    }

}
