<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\brand_categorieRepository;
use App\Entities\BrandCategorie;
use App\Validators\BrandCategorieValidator;

/**
 * Class BrandCategorieRepositoryEloquent
 * @package namespace App\Repositories;
 */
class BrandCategorieRepositoryEloquent extends BaseRepository implements BrandCategorieRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BrandCategorie::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
