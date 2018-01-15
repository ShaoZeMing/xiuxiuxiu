<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_malfunctionRepository;
use App\Entities\MerchantMalfunction;
use App\Validators\MerchantMalfunctionValidator;

/**
 * Class MerchantMalfunctionRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantMalfunctionRepositoryEloquent extends BaseRepository implements MerchantMalfunctionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantMalfunction::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
