<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\platformBillRepository;
use App\Entities\PlatformBill;
use App\Validators\PlatformBillValidator;

/**
 * Class PlatformBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class PlatformBillRepositoryEloquent extends BaseRepository implements PlatformBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PlatformBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
