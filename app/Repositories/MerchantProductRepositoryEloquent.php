<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_productRepository;
use App\Entities\MerchantProduct;
use App\Validators\MerchantProductValidator;

/**
 * Class MerchantProductRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantProductRepositoryEloquent extends BaseRepository implements MerchantProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantProduct::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
