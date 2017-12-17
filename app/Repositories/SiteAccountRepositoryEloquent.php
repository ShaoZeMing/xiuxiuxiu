<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\site_accountRepository;
use App\Entities\SiteAccount;
use App\Validators\SiteAccountValidator;

/**
 * Class SiteAccountRepositoryEloquent
 * @package namespace App\Repositories;
 */
class SiteAccountRepositoryEloquent extends BaseRepository implements SiteAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SiteAccount::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
