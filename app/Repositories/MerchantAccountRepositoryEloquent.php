<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchantAccountRepository;
use App\Entities\MerchantAccount;
use App\Validators\MerchantAccountValidator;

/**
 * Class MerchantAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantAccountRepositoryEloquent extends BaseRepository implements MerchantAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantAccount::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
