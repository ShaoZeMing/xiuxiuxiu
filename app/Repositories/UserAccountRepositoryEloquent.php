<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserAccountRepository;
use App\Entities\UserAccount;
use App\Validators\UserAccountValidator;

/**
 * Class UserAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserAccountRepositoryEloquent extends BaseRepository implements UserAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserAccount::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
