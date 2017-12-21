<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Brand;
use App\Entities\Categorie;
use App\Entities\Malfunction;
use App\Entities\Merchant;
use App\Entities\Product;
use App\Entities\ServiceType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class MerchantController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('商家列表');
            $content->description('这些都是老板，好生对待');
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
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑商家');
            $content->description('注意哈，有些不能修改的要注意哈');

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
        return Admin::grid(Merchant::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('名称');
            $grid->mobile('手机号');
            $grid->nickname('昵称');
            $grid->full_name();
            $grid->face()->display(function ($avatar) {
                return "<img src='{$avatar}' />";
            });
            $grid->profile()->postcode('Post code');
            $grid->profile()->address();
            $grid->position('Position');
            $grid->profile()->color();
            $grid->profile()->start_at('开始时间');
            $grid->profile()->end_at('结束时间');

            $grid->column('column1_not_in_table')->display(function () {
                return 'full name:'.$this->full_name;
            });

            $grid->column('column2_not_in_table')->display(function () {
                return $this->email.'#'.$this->profile['color'];
            });

            $grid->tags()->display(function ($tags) {
                $tags = collect($tags)->map(function ($tag) {
                    return "<code>{$tag['name']}</code>";
                })->toArray();

                return implode('', $tags);
            });

            $grid->created_at();
            $grid->updated_at();

            $grid->filter(function ($filter) {
                $filter->like('username');
                $filter->like('email');
                $filter->like('profile.postcode');
                $filter->between('profile.start_at')->datetime();
                $filter->between('profile.end_at')->datetime();
            });

            $grid->actions(function ($actions) {
                if ($actions->getKey() % 2 == 0) {
                    $actions->append('<a href="/" class="btn btn-xs btn-danger">detail</a>');
                }
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
        return Admin::form(Merchant::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->mobile('mobile', '电话');
            $form->text('name', '名称');
            $data = Categorie::orderBy('sort', 'desc')->get();
            $selectData = [];
            $data->each(function ($data) use (&$selectData) {
                $selectData[$data->id] = $data->name;
            });
            $form->select('cat_id', '分类')->options($selectData);
            $data = ServiceType::orderBy('sort', 'desc')->get();
            $selectData = [];
            $data->each(function ($data) use (&$selectData) {
                $selectData[$data->id] = $data->name;
            });
            $form->select('service_type_id', '服务类型')->options($selectData);
            $form->textarea('desc', '描述');
            $form->radio('state', '状态')->options(['0' => '非公开', '1' => '公开'])->default(1);
            $form->number('sort', '排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }


}
