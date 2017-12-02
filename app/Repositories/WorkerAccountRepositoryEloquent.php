<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\workerAccountRepository;
use App\Entities\WorkerAccount;
use App\Validators\WorkerAccountValidator;

/**
 * Class WorkerAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
class WorkerAccountRepositoryEloquent extends BaseRepository implements WorkerAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WorkerAccount::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
