<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\notice_receiverRepository;
use App\Entities\NoticeReceiver;
use App\Validators\NoticeReceiverValidator;

/**
 * Class NoticeReceiverRepositoryEloquent
 * @package namespace App\Repositories;
 */
class NoticeReceiverRepositoryEloquent extends BaseRepository implements NoticeReceiverRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NoticeReceiver::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
