<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\BrandM;
use App\Entities\CategorieM;
use App\Entities\Malfunction;
use App\Entities\MerchantProduct;
use App\Entities\Order;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Column;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ShaoZeMing\Merchant\Layout\Row;
use ShaoZeMing\Merchant\Widgets\Box;

class OrderController extends Controller
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
            $content->header('工单管理');
//            $content->description('这些都是工单');
            $content->row(function (Row $row) {
                $row->column(2, function (Column $column) {
//                    $box =new Box('二级列表', view('web.merchant.order.left_list'));
//                    $box->removable();
//                    $box->collapsable();
//                    $box->style('info');
//                    $box->solid();
//                    $column->append($box);
                    $column->append( view('web.merchant.order.left_list'));
                });
                $row->column(10, function (Column $column) {
                    $column->append($this->grid());
                });
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
            $content->row(function (Row $row) {
                $row->column(2, function (Column $column) {
//                    $box =new Box('二级列表', view('web.merchant.order.left_list'));
//                    $box->removable();
//                    $box->collapsable();
//                    $box->style('info');
//                    $box->solid();
//                    $column->append($box);
                    $column->append( view('web.merchant.order.left_list'));
                });
                $row->column(10, function (Column $column) {
                    $column->append($this->form());
                });
            });
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
            Log::info('删除工单',[$id,__METHOD__]);
            $merchant = getMerchantInfo();
            $res = $merchant->orders()->detach($id);
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
        return Merchant::grid(Order::class, function (Grid $grid) {

            $grid->model()->where('createdable_id',getMerchantId())->where('createdable_type',\App\Entities\Merchant::class)->where('state',">",0)->orderBy('id','desc');
            $grid->column('order_no', '工单号');
            $grid->column('service_type', '服务类型');
            $grid->column('cat_name', '品类名称');
            $grid->column('brand_name', '品牌名称');
            $grid->column('product_name', '产品名称');
            $grid->column('malfunction_name', '故障类型');
            $grid->column('order_desc', '其他说明');
            $grid->column('state', '工单状态');
            $grid->column('order_source', '工单来源');
            $grid->column('created_at', '创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('order_no','工单号');
                $filter->equal('brand_id','品牌')->select(BrandM::selectMerchantOptions());
                $filter->equal('cat_id','工单品类')->select(CategorieM::selectMerchantOptions());
                $filter->equal('state','工单状态')->select(Order::$getMerchantStateOptions);
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
        return Merchant::form(Order::class, function (Form $form) {
            $form->select('cat_id', '品类')->options(CategorieM::selectMerchantOptions('—'))->load('product_id','/merchant/api/cat/products');
            $form->select('product_id', '产品')->options(getMerchantInfo()->products()->get()->pluck('product_name', 'id'))->load('malfunction_id','/merchant/api/product/malfunctions');
            $form->select('malfunction_id', '故障类型')->options(getMerchantInfo()->malfunctions()->get()->pluck('malfunction_name', 'id'));
            $form->textarea('product_desc', '产品描述');
            $form->saved(function (Form $form){
                getMerchantInfo()->products()->syncWithoutDetaching($form->model()->id);
            });
        });
    }




}
