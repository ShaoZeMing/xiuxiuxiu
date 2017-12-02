<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\workerBillRepository;
use App\Entities\WorkerBill;
use App\Validators\WorkerBillValidator;

/**
 * Class WorkerBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class WorkerBillRepositoryEloquent extends BaseRepository implements WorkerBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WorkerBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
