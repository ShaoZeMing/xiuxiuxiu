<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Product;
use App\Entities\ServiceTypeM;
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
//            $content->body($this->grid());
            $content->row(function (Row $row) {
                $row->column(6, $this->grid()->render());
                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {

                            Log::info('idssss',[ServiceTypeM::$ids,getMerchantId()]);
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('api/merchants/'.getMerchantId().'/service_types'));
                            $form->multipleSelect('service_types', '服务类型名称')->options(ServiceTypeM::whereNotIn('id',ServiceTypeM::$ids)->get()->pluck('service_type_name', 'id'));
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Merchant::grid(ServiceTypeM::class, function (Grid $grid) {

//            $grid->id('ID')->sortable();
            $grid->model()->orderBy('service_type_sort');
            $grid->column('service_type_name', '名称');
            $grid->service_type_desc('描述');
//            $grid->created_at('创建时间');
//            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {// 设置created_at字段的范围查询
                $filter->between('created_at', 'Created Time')->datetime();
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
//            $form->display('id', 'ID');
            $form->text('service_type_name', '名称');
            $form->textarea('service_type_desc', '描述');
//            $form->number('service_type_sort', '排序');
//            $form->switch('service_type_state','状态')->default(1);
        });
    }
}
