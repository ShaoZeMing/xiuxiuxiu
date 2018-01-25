<?php

namespace App\Http\Controllers\Web\Merchant;

use App\Http\Controllers\Controller;
use ShaoZeMing\Merchant\Controllers\Dashboard;
use ShaoZeMing\Merchant\Facades\Merchant;
use ShaoZeMing\Merchant\Layout\Column;
use ShaoZeMing\Merchant\Layout\Content;
use ShaoZeMing\Merchant\Layout\Row;

class HomeController extends Controller
{
    public function index()
    {
        return Merchant::content(function (Content $content) {

            $content->header('Dashboard');
            $content->description('Description...');
            $content->row(view('web.merchant.order.create'));
            $content->row(function (Row $row) {
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
        });
    }
}
