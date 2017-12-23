<?php
namespace App\Traits;

/**
 *  SequenceTrait.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: SequenceTrait.php 2017-03-19 下午12:22 $
 */
trait SequenceTrait
{
    /**
     * Binds creating/saving events to create id and order_no (and also prevent them from being overwritten).
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @return void
     */
    protected static function boot()
    {
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = app('sequence')->generate();

            foreach ($model as $k =>$v){
                if(is_null($v)){
                    unset($model->{$k});
                }
            }
            \Illuminate\Support\Facades\Log::info($model);
        });


        static::saving(function ($model) {
            // What's that, trying to change the order_no huh?  Nope, not gonna happen.
            $originalId = $model->getOriginal('id');

            if ($originalId !== $model->id) {
                $model->id = $originalId;
            }
        });

        parent::boot();
    }
}
