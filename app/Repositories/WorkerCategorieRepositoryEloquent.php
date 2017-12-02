<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\worker_categorieRepository;
use App\Entities\WorkerCategorie;
use App\Validators\WorkerCategorieValidator;

/**
 * Class WorkerCategorieRepositoryEloquent
 * @package namespace App\Repositories;
 */
class WorkerCategorieRepositoryEloquent extends BaseRepository implements WorkerCategorieRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WorkerCategorie::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
