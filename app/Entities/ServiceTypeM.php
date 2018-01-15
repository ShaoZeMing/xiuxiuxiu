<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;


/**
 * App\Entities\ServiceType
 *
 * @property string $id
 * @property string $service_type_name
 * @property string $service_type_desc
 * @property int $service_type_state
 * @property int $service_type_level
 * @property int $service_type_sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Malfunction[] $malfunctions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereServiceTypeState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServiceTypeM extends ServiceType
{

    public static $ids = [];
    public $table='service_types';

    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectOptions()
    {
        return self::all()->pluck('service_type_name', 'id');
    }


    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectMerchantOptions()
    {
        return getMerchantInfo()->serviceTypes()->get()->pluck('service_type_name', 'id');
    }



    public static function paginate($perPage = null, $columns = array(), $pageName = 'page', $page = null)
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;

        $merchant = getMerchantInfo();
        $total = $merchant->serviceTypes();
        self::$ids = array_column($total->get()->toArray(),'id');
        Log::info('IIIIIDS',[self::$ids,__METHOD__]);
        $data = $merchant->serviceTypes()->offset($start)->limit($perPage)->get();
        dd($data);
        $paginator = new LengthAwarePaginator($data, $total->count(), $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

}
