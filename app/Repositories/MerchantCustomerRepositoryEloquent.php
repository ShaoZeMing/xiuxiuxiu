<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_customerRepository;
use App\Entities\MerchantCustomer;
use App\Validators\MerchantCustomerValidator;

/**
 * Class MerchantCustomerRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantCustomerRepositoryEloquent extends BaseRepository implements MerchantCustomerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantCustomer::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
