<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\platformAccountRepository;
use App\Entities\PlatformAccount;
use App\Validators\PlatformAccountValidator;

/**
 * Class PlatformAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
class PlatformAccountRepositoryEloquent extends BaseRepository implements PlatformAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PlatformAccount::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
