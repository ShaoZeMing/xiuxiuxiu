<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_service_typeRepository;
use App\Entities\MerchantServiceType;
use App\Validators\MerchantServiceTypeValidator;

/**
 * Class MerchantServiceTypeRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantServiceTypeRepositoryEloquent extends BaseRepository implements MerchantServiceTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantServiceType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
