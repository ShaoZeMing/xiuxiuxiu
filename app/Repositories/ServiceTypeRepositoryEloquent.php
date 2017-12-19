<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\service_typeRepository;
use App\Entities\ServiceType;
use App\Validators\ServiceTypeValidator;

/**
 * Class ServiceTypeRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ServiceTypeRepositoryEloquent extends BaseRepository implements ServiceTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ServiceType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
