<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\workerRepository;
use App\Entities\Worker;
use App\Validators\WorkerValidator;

/**
 * Class WorkerRepositoryEloquent
 * @package namespace App\Repositories;
 */
class WorkerRepositoryEloquent extends BaseRepository implements WorkerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Worker::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
