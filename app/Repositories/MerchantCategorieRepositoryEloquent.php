<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\merchant_categorieRepository;
use App\Entities\MerchantCategorie;
use App\Validators\MerchantCategorieValidator;

/**
 * Class MerchantCategorieRepositoryEloquent
 * @package namespace App\Repositories;
 */
class MerchantCategorieRepositoryEloquent extends BaseRepository implements MerchantCategorieRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MerchantCategorie::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
