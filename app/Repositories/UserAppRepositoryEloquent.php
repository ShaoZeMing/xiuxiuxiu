<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\user_appRepository;
use App\Entities\UserApp;
use App\Validators\UserAppValidator;

/**
 * Class UserAppRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserAppRepositoryEloquent extends BaseRepository implements UserAppRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserApp::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
