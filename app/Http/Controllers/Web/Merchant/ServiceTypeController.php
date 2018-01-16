<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\MerchantServiceType;
use App\Entities\Product;
use App\Entities\ServiceTypeM;
use App\Entities\ServiceTypeMG;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ShaoZeMing\Merchant\Tree;
use ShaoZeMing\Merchant\Layout\Column;
use ShaoZeMing\Merchant\Layout\Row;
use ShaoZeMing\Merchant\Widgets\Box;

class ServiceTypeController extends Controller
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
            $content->header('服务类型管理');
            $content->description('工单需要服务的类型，比如安装，维修，送修，机修，换机');
            $content->row(function (Row $row) {
                $row->column(6, $this->grid()->render());
                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {

                            Log::info('idssss',[self::$ids,getMerchantId()]);
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('api/merchants/'.getMerchantId().'/service_types'));
                            $form->multipleSelect('service_types', '服务类型')->options(ServiceTypeM::whereNotIn('id',self::$ids)->get()->pluck('service_type_name', 'id'));
                            $column->append((new Box('添加系统已有服务类型', $form))->style('success'));
                        });
                    });
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('service_types'));
                            $form->text('service_type_name', '名称')->rules('required');
                            $form->textarea('service_type_desc', '描述')->default('');
                            $form->hidden('created_id')->default(getMerchantId());
                            $column->append((new Box('添加自定义服务类型', $form))->style('success'));
                        });
                    });
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
        return Merchant::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

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
            Log::info('删除服务类型',[$id,__METHOD__]);
            $merchant = getMerchantInfo();
            $res = $merchant->serviceTypes()->detach($id);
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
        return Merchant::grid(ServiceTypeM::class, function (Grid $grid) {

            $ids  = MerchantServiceType::where('merchant_id',getMerchantId())->get(['service_type_id'])->toArray();
            self::$ids = array_column($ids, 'service_type_id');
            $grid->model()->whereIn('id', self::$ids)->orderBy('service_type_sort');
            $grid->column('service_type_name', '名称');
            $grid->service_type_desc('描述');
            $grid->actions(function (Grid\Displayers\Actions $actions)use($grid) {
                if (ServiceTypeM::find($actions->getKey())->created_id != getMerchantId()) {
                    $actions->disableEdit();
                }
            });
            $grid->disableCreation();
            $grid->disableExport();
            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
                $filter->disableIdFilter();
                $filter->like('service_type_name','类型名称');
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
        return Merchant::form(ServiceTypeM::class, function (Form $form) {
            $form->text('service_type_name', '名称');
            $form->textarea('service_type_desc', '描述');
            $form->hidden('created_id')->default(getMerchantId());
            $form->saved(function (Form $form){
                getMerchantInfo()->serviceTypes()->syncWithoutDetaching($form->model()->id);
            });
        });
    }
}
