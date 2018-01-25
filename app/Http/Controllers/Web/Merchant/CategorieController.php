<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Entities\Brand;
use App\Entities\BrandM;
use App\Entities\Categorie;
use App\Entities\CategorieM;
use App\Entities\Malfunction;
use App\Http\Controllers\Controller;
use App\Repositories\CategorieRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ShaoZeMing\Merchant\Controllers\ModelForm;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Form;
use ShaoZeMing\Merchant\Grid;
use ShaoZeMing\Merchant\Layout\Column;
use ShaoZeMing\Merchant\Layout\Content;
use ShaoZeMing\Merchant\Layout\Row;
use ShaoZeMing\Merchant\Tree;
use ShaoZeMing\Merchant\Widgets\Box;

class CategorieController extends Controller
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

            $content->header('分类管理');
            $content->description('企业可在右侧搜索添加系统已有分类(推荐)，如系统分类未满足你的需求，可在右下角自定义添加。');
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            Log::info('ids',[self::$ids,getMerchantId()]);
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('api/merchants/'.getMerchantId().'/cats'));
                            $form->multipleSelect('cats', '品类名称')->options(CategorieM::whereNotIn('id',self::$ids)->get()->pluck('cat_name', 'id'));
                            $column->append((new Box('添加系统已有品类', $form))->style('success'));
                        });
                    });
                    $column->row(function (Row $row) {
                        $row->column(12, function (Column $column) {
                            $form = new \ShaoZeMing\Merchant\Widgets\Form();
                            $form->action(merchant_base_path('cats'));
                            $form->select('cat_parent_id', '父级')->options(CategorieM::selectMerchantOptions());
                            $form->text('cat_name', '名称')->rules('required');
                            $form->textarea('cat_desc', '描述')->default('');
                            $form->image('cat_logo', 'LOGO')->resize(200, 200)->uniqueName()->removable();
                            $mBrands =  getMerchantInfo()->brands()->count();
                            if($mBrands){
                                $form->multipleSelect('brands', '经营品牌')->options(BrandM::selectOptions());
                            }
                            $form->hidden('cat_state', '状态')->default(0);
                            $form->hidden('created_id')->default(getMerchantId());
                            $column->append((new Box('添加自定义品类', $form))->style('success'));
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
        return CategorieM::tree(function (Tree $tree) {
            $tree->query(function ($model) {
                $merchant =getMerchantInfo();
                $cats = $merchant->cats();
                self::$ids = array_column($cats->get()->toArray(),'id');
                return $cats;
            });
//            $isSave = false;
            $tree->branch(function ($branch){
                $payload = "<img src='{$branch['cat_logo']}' width='40'>&nbsp;<strong>{$branch['cat_name']}</strong>";
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

            $content->header('修改分类');
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

            $content->header('新增分类');
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
            Log::info('删除分类',[$id,__METHOD__]);
            $merchant = getMerchantInfo();
            $id= Categorie::getDelIds($id);
            $res = $merchant->cats()->detach($id);
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
        return Merchant::grid(CategorieM::class, function (Grid $grid) {

            $grid->model()->OrderBy('cat_parent_id');
            $grid->column('cat_name', '分类名称');
            $grid->cat_logo('LOGO')->display(function ($name) {
                return "<img src='{$name}' width='40'>";
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
                $filter->like('cat_name', '名称');
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
        return Merchant::form(CategorieM::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('cat_name', '名称')->rules('required');
            $form->select('cat_parent_id', '父级')->options(CategorieM::selectOptions());
            $form->textarea('cat_desc', '描述')->default('');
            $form->image('cat_logo', 'LOGO')->resize(200, 200)->uniqueName()->removable();
            $mBrands =  getMerchantInfo()->brands()->count();
            if($mBrands){
                $form->multipleSelect('brands', '经营品牌')->options(BrandM::selectOptions());
            }
            $form->hidden('created_id')->default(getMerchantId());
            $form->saved(function (Form $form){
                $cats = Categorie::getAddIds([$form->model()->id]);
                getMerchantInfo()->cats()->syncWithoutDetaching($cats);
            });

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
        $data = CategorieM::where('cat_name', 'like', "%$q%")->get(['id', 'cat_name as text']);
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
        Log::info($q,[__METHOD__]);
        $mftIds = cache(getMerchantId().'_mft_ids')?:[];
        if(!$q){
            $data = Malfunction::whereNotIn('id', $mftIds)->get(['id', DB::raw('malfunction_name as text')]);
            return $data;
        }
        $q = Categorie::getDelIds($q);
        $data = Malfunction::whereNotIn('id', $mftIds)->whereIn('cat_id', $q)->get(['id', DB::raw('malfunction_name as text')]);
        return $data;
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function apiProducts(Request $request, CategorieRepositoryEloquent $categorieRepository)
    {
        $q = $request->get('q');
        if(!$q){
            return [];
        }
        $res = getMerchantInfo()->products()->get(['id'])->toArray();
        $ids = array_column($res,'id');
        return $categorieRepository->find($q)->products()->whereIn('id',$ids)->get(['products.id', DB::raw('product_name as text')]);
    }
}
