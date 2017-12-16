<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\areaRepository;
use App\Entities\Area;
use App\Validators\AreaValidator;

/**
 * Class AreaRepositoryEloquent
 * @package namespace App\Repositories;
 */
class AreaRepositoryEloquent extends BaseRepository implements AreaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Area::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    public function districtsToTree($pid = 0)
    {

        $where = [
            'parent_id' => $pid,
        ];

        $provinces = $this->with('sub')->findWhere($where);
        $provinces->each(function ($province) {
            $province->sub->each->sub;
        });

        return $provinces;
    }
}
