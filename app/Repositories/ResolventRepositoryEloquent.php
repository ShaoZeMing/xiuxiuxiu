<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\resolventRepository;
use App\Entities\Resolvent;
use App\Validators\ResolventValidator;

/**
 * Class ResolventRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ResolventRepositoryEloquent extends BaseRepository implements ResolventRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Resolvent::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
