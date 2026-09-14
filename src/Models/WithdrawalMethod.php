<?php

namespace TomatoPHP\FilamentWithdrawals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use TomatoPHP\FilamentWithdrawals\Database\Factories\WithdrawalMethodFactory;

class WithdrawalMethod extends Model implements HasMedia
{
    /** @use HasFactory<WithdrawalMethodFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'currency',
        'rate',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(WithdrawalMethodOption::class, 'withdrawal_method_id', 'id')->orderBy('order');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class, 'withdrawal_method_id', 'id');
    }

    protected static function newFactory(): WithdrawalMethodFactory
    {
        return WithdrawalMethodFactory::new();
    }
}
