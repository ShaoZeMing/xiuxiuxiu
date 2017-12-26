<?php

namespace App\Services\Vendor;

use App\Entities\Order;
use App\Entities\Site;
use App\Entities\User;
use App\Entities\Worker;
use Illuminate\Foundation\Application;
use Shaozeming\LumenPostgis\Geometries\GeomPoint;


/**
 * Class Geom
 * @package App\Services\Vendor
 * 距离搜索服务
 */
class Geom
{
    /**
     */
    private $application;

    public function __construct(Application $application)
    {

        $this->application = $application;
    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $lng
     * @param $lat
     * @param int $dist
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 搜索范围工单
     */
    public static function getOrders($lng, $lat, $dist = 30000,$where = [],$columns=[])
    {

        $geom = new GeomPoint($lng, $lat);//获取geom数据
        $distSql = "ST_DistanceSphere(geom,{$geom})";

        $orders = Order::selectRaw(" id,order_no,price,order_type,biz_type,cat_id,cat,createdable_id,createdable_type,created_name,created_logo,full_address,{$distSql} as dist")
            ->addSelect($columns)
            ->whereRaw("{$distSql} < {$dist} AND state = 0")
            ->where($where)
            ->orderByRaw("$distSql")
            ->orderBy('id','DESC')
            ->paginate(15);

        return $orders;

    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $lng
     * @param $lat
     * @param int $dist
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 搜索范围师傅
     */
    public static function getWorkers($lng, $lat,$dist = 30000,$where = [],$columns=[])
    {

        $geom = new GeomPoint($lng, $lat);//获取geom数据
        $distSql = "ST_DistanceSphere(worker_geom,{$geom})";
        $workers = Worker::selectRaw(" id,worker_mobile,worker_name,worker_face,worker_state,worker_is_notice,worker_full_address,{$distSql} as dist")
            ->addSelect($columns)
            ->whereRaw("{$distSql} < {$dist}")
            ->where($where)
            ->orderByRaw($distSql)
            ->orderBy('id','DESC')
            ->paginate(15);

        return $workers;

    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $lng
     * @param $lat
     * @param int $dist
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 搜索范围网点
     */
    public static function getSites($lng, $lat,$dist = 30000,$where = [],$columns=[])
    {

        $geom = new GeomPoint($lng, $lat);//获取geom数据
        $distSql = "ST_DistanceSphere(site_geom,{$geom})";
        $sites = Site::selectRaw(" id,site_mobile,site_name,site_face,site_state,site_is_notice,site_full_address,{$distSql} as dist")
            ->addSelect($columns)
            ->whereRaw("{$distSql} < {$dist}")
            ->where($where)
            ->orderByRaw($distSql)
            ->orderBy('id','DESC')
            ->paginate(15);

        return $sites;

    }


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @param $lng
     * @param $lat
     * @param int $dist
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUsers($lng, $lat,$dist = 30000,$where = [],$columns=[])
    {

        $geom = new GeomPoint($lng, $lat);//获取geom数据
        $distSql = "ST_DistanceSphere(user_geom,{$geom})";
        $users = User::selectRaw(" id,user_mobile,user_name,user_face,user_state,user_is_notice,user_full_address,{$distSql} as dist")
            ->addSelect($columns)
            ->whereRaw("{$distSql} < {$dist}")
            ->where($where)
            ->orderByRaw($distSql)
            ->orderBy('id','DESC')
            ->paginate(15);

        return $users;

    }



}
