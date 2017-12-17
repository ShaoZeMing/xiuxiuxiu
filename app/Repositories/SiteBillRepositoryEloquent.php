<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\site_billRepository;
use App\Entities\SiteBill;
use App\Validators\SiteBillValidator;

/**
 * Class SiteBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class SiteBillRepositoryEloquent extends BaseRepository implements SiteBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SiteBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
