<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserBillRepository;
use App\Entities\UserBill;
use App\Validators\UserBillValidator;

/**
 * Class UserBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserBillRepositoryEloquent extends BaseRepository implements UserBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
