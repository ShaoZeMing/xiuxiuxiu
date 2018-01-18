<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\CategorieM;
use App\Entities\Malfunction;
use App\Entities\MerchantMalfunction;
use App\Entities\ServiceTypeM;
use App\Http\Controllers\Controller;
use App\Repositories\ResolventRepositoryEloquent;
use Illuminate\Support\Facades\Log;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ShaoZeMing\Merchant\Layout\Column;
use ShaoZeMing\Merchant\Layout\Row;
use ShaoZeMing\Merchant\Widgets\Box;

class MalfunctionController extends Controller
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
            $content->header('故障列表');
            $content->description('所有故障的列表');
            $content->row(function (Row $row) {
                $row->column(6, $this->grid()->render());
                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            cache([getMerchantId().'_mft_ids'=>self::$ids],3);
                            Log::info(cache(getMerchantId().'_mft_ids'),['缓存']);
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('api/merchants/'.getMerchantId().'/malfunctions'));
                            $form->select('cat_id', '产品分类')->options(CategorieM::selectMerchantOptions('—'))->load('malfunctions','/merchant/api/cat/malfunctions');
                            $form->multipleSelect('malfunctions', '故障类型')->options(Malfunction::whereNotIn('id',self::$ids)->get()->pluck('malfunction_name', 'id'));
                            $column->append((new Box('添加系统已有故障', $form))->style('success'));
                        });
                    });
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->select('cat_id', '所属品类')->options(CategorieM::selectMerchantOptions('—'))->load('products','/merchant/api/cat/products');
                            $form->text('malfunction_name', '故障名称')->rules('required');
                            $form->select('service_type_id', '服务类型')->options(ServiceTypeM::selectMerchantOptions());
                            $form->multipleSelect('products', '产品关联')->options(getMerchantInfo()->products()->get()->pluck('product_name', 'id'));
                            $form->textarea('malfunction_desc', '描述')->default('');
                            $form->hasMany('resolvents','添加解决方法',function (Form\NestedForm $form) {
                                $form->text('resolvent_name','方法名称')->rules('required');
                                $form->textarea('resolvent_desc','方法详解')->rules('required');
                                $form->url('resolvent_url','视频地址')->default('');
                            });
                            $form->hidden('created_id')->default(getMerchantId());
                            $column->append((new Box('添加故障', $form))->style('success'));
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

            $content->header('编辑故障');
            $content->description('description');

            $content->body($this->form()->edit($id));
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
            $res = $merchant->malfunctions()->detach($id);
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
            $ids  = MerchantMalfunction::where('merchant_id',getMerchantId())->get(['malfunction_id'])->toArray();
            self::$ids = array_column($ids, 'malfunction_id');
            $grid->model()->whereIn('id', self::$ids)->orderBy('malfunction_sort');
            $grid->column('malfunction_name', '故障名称');
            $grid->column('cat.cat_name', '所属分类');
            $grid->products('关联产品')->display(function ($products) {
                $productHtml = '';
                foreach ($products as $k => $v){
                    $productHtml .= "<span class='label label-success' style=''>{$v['product_name']}</span>&nbsp;";
                    if(($k+1)%3==0){
                        $productHtml .= '<br><br>';
                    }
                }
                return $productHtml;
            });
            $grid->column('serviceType.service_type_name', '服务类型');
            $grid->actions(function (Grid\Displayers\Actions $actions)use($grid) {
                if (Malfunction::find($actions->getKey())->created_id != getMerchantId()) {
                    $actions->disableEdit();
                }
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('malfunction_name','名称');
                $filter->equal('service_type_id','服务类型')->select(ServiceTypeM::selectMerchantOptions());
                $filter->equal('cat_id','分类')->select(CategorieM::selectMerchantOptions('—'));
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
            $form->select('cat_id', '所属品类')->options(CategorieM::selectMerchantOptions('—'))->load('products','/merchant/api/cat/products');
            $form->text('malfunction_name', '故障名称')->rules('required');
            $form->select('service_type_id', '服务类型')->options(ServiceTypeM::selectMerchantOptions());
            $form->multipleSelect('products', '产品关联')->options(getMerchantInfo()->products()->get()->pluck('product_name', 'id'));
            $form->textarea('malfunction_desc', '描述')->default('');
            $form->hasMany('resolvents','添加解决方法',function (Form\NestedForm $form) {
                $form->text('resolvent_name','方法名称')->rules('required');
                $form->textarea('resolvent_desc','方法详解')->rules('required');
                $form->url('resolvent_url','视频地址')->default('');
            });
            $form->hidden('created_id')->default(getMerchantId());
            $form->saved(function (Form $form){
                getMerchantInfo()->malfunctions()->syncWithoutDetaching($form->model()->id);
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
