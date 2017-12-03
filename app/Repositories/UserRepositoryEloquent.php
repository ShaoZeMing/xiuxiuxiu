<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use App\Entities\User;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }



    /**
     * 搜索订单.
     *
     * @param string $filePath
     * @param bool $immutable
     * @param string $point 经纬度
     * @param int $dist 距离范围
     * @param int $limit 查询多少个
     * @param int $status 师傅状态
     *
     * @return bool
     */

    public function selectData($point, $dist, $status, $limit,$offset,$sort_type)
    {

        $sql = $this->getSearchSql($point, $dist, $status, $limit,$offset,$sort_type);
        try {
            $result = DB::select($sql);
            foreach($result as $k => $value){
                $value->big_cat = $value->big_cat.'|'. $value->middle_cat;
            }
        } catch (\Exception $e) {
            Log::error('c=OrderApiRepository f=selectData  msg=' . $e->getMessage());
            return false;
        }

        return $result;
    }




    /**
     * 拼装搜索数据GIS sql语句.
     *
     * @param string $filePath
     * @param array $data 要插入的字段/数据
     *
     * @return string  sql
     */

    protected function getSearchSql($point, $dist, $status, $limit,$offset,$sort_type)
    {


        $point = "point($point)";
        $distSql = "ST_DistanceSphere(geom,ST_GeomFromText('$point',4326))";

        //获取排序规则
        $orderBy='';
        switch($sort_type){
            case 'time_dist':
                $orderBy ='id DESC , '.$distSql ;
                break;
            case 'dist_time':
                $orderBy = $distSql.', id DESC ';
                break;
        }

        $sql = "select id,order_no,merchant_logo,price,order_type,biz_type,middle_cat,merchant_id,merchant_name,user_lat,user_lng,full_address,big_cat,published_at,
            {$distSql} dist
            from orders
            where {$distSql} < {$dist}
            AND state = {$status}
            order by {$orderBy}
            LIMIT {$limit} OFFSET {$offset}";
//        Log::info('sql='.$sql);
        return $sql;

    }

}
