<?php

namespace TomatoPHP\FilamentWithdrawals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WithdrawalRequest extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * @var array
     */
    protected $fillable = [
        'withdrawal_method_id',
        'model_type',
        'model_id',
        'amount',
        'rate',
        'currency',
        'description',
        'date',
        'time',
        'status',
        'payload',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        "payload" => "array"
    ];

    /**
     * @return MorphTo
     */
    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(WithdrawalMethod::class, 'withdrawal_method_id', 'id');
    }
}
