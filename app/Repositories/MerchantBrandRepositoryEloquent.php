<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_brandRepository;
use App\Entities\MerchantBrand;
use App\Validators\MerchantBrandValidator;

/**
 * Class MerchantBrandRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantBrandRepositoryEloquent extends BaseRepository implements MerchantBrandRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantBrand::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
