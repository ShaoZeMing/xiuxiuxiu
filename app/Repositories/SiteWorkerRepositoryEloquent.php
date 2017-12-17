<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\site_workerRepository;
use App\Entities\SiteWorker;
use App\Validators\SiteWorkerValidator;

/**
 * Class SiteWorkerRepositoryEloquent
 * @package namespace App\Repositories;
 */
class SiteWorkerRepositoryEloquent extends BaseRepository implements SiteWorkerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SiteWorker::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
