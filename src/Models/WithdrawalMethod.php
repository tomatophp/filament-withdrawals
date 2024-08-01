<?php

namespace TomatoPHP\FilamentWithdrawals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WithdrawalMethod extends Model implements HasMedia
{
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        return $this->hasMany(WithdrawalMethodOption::class, 'withdrawal_method_id', 'id')->orderBy('order', 'asc');
    }

    public function requests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'withdrawal_method_id', 'id');
    }
}
