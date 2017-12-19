<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\product_malfunctionRepository;
use App\Entities\ProductMalfunction;
use App\Validators\ProductMalfunctionValidator;

/**
 * Class ProductMalfunctionRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ProductMalfunctionRepositoryEloquent extends BaseRepository implements ProductMalfunctionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductMalfunction::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
