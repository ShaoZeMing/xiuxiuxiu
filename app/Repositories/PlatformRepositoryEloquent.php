<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\platformRepository;
use App\Entities\Platform;
use App\Validators\PlatformValidator;

/**
 * Class PlatformRepositoryEloquent
 * @package namespace App\Repositories;
 */
class PlatformRepositoryEloquent extends BaseRepository implements PlatformRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Platform::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
