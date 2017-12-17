<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_siteRepository;
use App\Entities\MerchantSite;
use App\Validators\MerchantSiteValidator;

/**
 * Class MerchantSiteRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantSiteRepositoryEloquent extends BaseRepository implements MerchantSiteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantSite::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
