<?php

namespace TomatoPHP\FilamentWithdrawals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use TomatoPHP\FilamentWithdrawals\Database\Factories\WithdrawalRequestFactory;

class WithdrawalRequest extends Model implements HasMedia
{
    /** @use HasFactory<WithdrawalRequestFactory> */
    use HasFactory;

    use InteractsWithMedia;

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
        'updated_at',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(WithdrawalMethod::class, 'withdrawal_method_id', 'id');
    }

    protected static function newFactory(): WithdrawalRequestFactory
    {
        return WithdrawalRequestFactory::new();
    }
}
