<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\NoticeReceiver
 *
 * @property int $id
 * @property int $state
 * @property int $notice_id
 * @property string $wx_msg_id
 * @property int $noticeable_id
 * @property string $noticeable_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereNoticeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereNoticeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereNoticeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\NoticeReceiver whereWxMsgId($value)
 * @mixin \Eloquent
 */
class NoticeReceiver extends BaseModel
{
    protected $fillable = [];




    public function notice(){
        return $this->belongsTo(Notice::class);
    }

    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function noticeable(){
        return $this->morphTo();
    }
}
