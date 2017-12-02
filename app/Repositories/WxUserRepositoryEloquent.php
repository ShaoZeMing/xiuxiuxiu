<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\wx_userRepository;
use App\Entities\WxUser;
use App\Validators\WxUserValidator;

/**
 * Class WxUserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class WxUserRepositoryEloquent extends BaseRepository implements WxUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WxUser::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
