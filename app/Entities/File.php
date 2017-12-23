<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Traits\SequenceTrait;

/**
 * App\Entities\File
 *
 * @property int $id
 * @property string $filename
 * @property string $file_ext
 * @property string $file_size
 * @property string $file_type
 * @property string $b_path
 * @property string $m_path
 * @property string $s_path
 * @property int $uploadable_id
 * @property string $uploadable_type
 * @property int $image_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereBPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereImageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereMPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereSPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereUploadableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\File whereUploadableType($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $uploadable
 */
class File extends BaseModel
{
    protected $guarded = [];


    /**
     * @author ShaoZeMing
     * @email szm19920426@gmail.com
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function uploadable(){
        return $this->morphTo();
    }

}
