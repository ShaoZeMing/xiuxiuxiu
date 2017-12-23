<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;
use Shaozeming\LumenPostgis\Eloquent\PostgisTrait;

/**
 * App\Entities\Worker
 *
 * @property int $id
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $face
 * @property string $pwd
 * @property string $pay_pwd
 * @property string $birthday
 * @property int $sex
 * @property int $state
 * @property int $is_notice
 * @property float $lat
 * @property float $lng
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $full_address
 * @property int $cancel_order_cnt
 * @property int $success_order_cnt
 * @property int $brokerage_percent
 * @property int $wx_user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCancelOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker wherePayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker wherePwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereSuccessOrderCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWxUserId($value)
 * @mixin \Eloquent
 * @property string $worker_mobile
 * @property string $worker_name
 * @property string $worker_nickname
 * @property string $worker_face
 * @property string $worker_pwd
 * @property string $worker_pay_pwd
 * @property string $worker_birthday
 * @property int $worker_sex
 * @property int $worker_state
 * @property int $worker_is_notice
 * @property float $worker_lat
 * @property float $worker_lng
 * @property string $worker_province
 * @property string $worker_city
 * @property string $worker_district
 * @property string $worker_address
 * @property string $worker_full_address
 * @property int $worker_cancel_cnt
 * @property int $worker_success_cnt
 * @property int $worker_brokerage_percent
 * @property-read \App\Entities\WorkerAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\WorkerBill[] $bills
 * @property-read \App\Entities\WxUser $wxUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerBrokeragePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerCancelCnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerFace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerIsNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerPayPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Worker whereWorkerSuccessCnt($value)
 */
class Worker extends BaseModel
{
    use PostgisTrait;

    protected $guarded = [];



    public function account()
    {
        return $this->hasOne(WorkerAccount::class,'id','id');
    }

    public function bills()
    {
        return $this->hasMany(WorkerBill::class);
    }

    public function wxUser()
    {
        return $this->belongsTo(WxUser::class);
    }
}
