<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\BrandM;
use App\Entities\Categorie;
use App\Entities\CategorieM;
use App\Http\Controllers\Controller;
use App\Repositories\BrandRepositoryEloquent;
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

class BrandController extends Controller
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

            $content->header('品牌管理');
            $content->description('企业可在右侧搜索添加系统已有品牌(推荐)，如系统品牌未满足你的需求，可在右下角自定义添加。');
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            Log::info('ids',[self::$ids,getMerchantId()]);
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('api/merchants/'.getMerchantId().'/brands'));
                            $form->multipleSelect('brands', '品牌名称')->options(BrandM::whereNotIn('id',self::$ids)->get()->pluck('brand_name', 'id'));
                            $column->append((new Box('添加系统已有品牌', $form))->style('success'));
                        });
                    });
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('brands'));
                            $form->select('brand_parent_id', '父级')->options(BrandM::selectOptions());
                            $form->text('brand_name', '名称')->rules('required');
                            $form->textarea('brand_desc', '描述')->default('');
                            $form->image('brand_logo', 'LOGO')->resize(200, 200)->uniqueName()->removable();
                            $mCats =  getMerchantInfo()->cats()->count();
                            if($mCats){
                                $form->multipleSelect('cats', '经营品类')->options(CategorieM::selectOptions());
                            }
                            $form->hidden('brand_state', '状态')->default(0);
                            $form->hidden('created_id')->default(getMerchantId());
                            $column->append((new Box('添加自定义品牌', $form))->style('success'));
                        });
                    });
                });
            });
        });
    }

        /**
         * @return \Encore\Admin\Tree
         */
        protected function treeView()
    {
        return BrandM::tree(function (Tree $tree) {
            $tree->query(function ($model) {
                $merchant =getMerchantInfo();
                $cats = $merchant->brands();
                self::$ids = array_column($cats->get()->toArray(),'id');
                return $cats;
            });
            $tree->branch(function ($branch){
                $payload = "<img src='{$branch['brand_logo']}' width='40'>&nbsp;<strong>{$branch['brand_name']}</strong>";
                return $payload;
            });
            $tree->disableCreate();
            $tree->disableSave();
//            $tree->disableRefresh();

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
            $content->header('修改品牌');
            $content->description('为了数据清晰，不建议频繁修改');
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
            $content->header('新增品牌');
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
            Log::info('删除品牌',[$id,__METHOD__]);
            $merchant = getMerchantInfo();
            $id= Brand::getDelIds($id);
            $res = $merchant->brands()->detach($id);
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
        return Merchant::grid(BrandM::class, function (Grid $grid) {
//            $grid->id('ID')->sortable();
            $grid->model()->orderBy('brand_sort');
            $grid->column('brand_name', '品牌名称');
            $grid->brand_logo('LOGO')->display(function ($name) {
                return "<img src='{$name}' width='40'>";
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

        return Merchant::form(BrandM::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->select('brand_parent_id', '父级')->options(BrandM::selectOptions());
            $form->text('brand_name', '品牌名称')->rules('required');
            $form->textarea('brand_desc', '品牌描述')->default('');
            $form->image('brand_logo', '品牌LOGO')->resize(200,200)->uniqueName()->removable();
            $mCats =  getMerchantInfo()->cats()->count();
            if($mCats){
                $form->multipleSelect('cats', '经营品类')->options(CategorieM::selectOptions());
            }
            $form->hidden('created_id')->default(getMerchantId());
            $form->saved(function (Form $form){
                $cats = Brand::getAddIds([$form->model()->id]);
                getMerchantInfo()->brands()->syncWithoutDetaching($cats);
            });
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
        return  $brandRepository->find($q)->cats()->get(['categories.id', DB::raw('brand_name as text')]);
    }
}
