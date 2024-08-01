<?php

namespace TomatoPHP\FilamentWithdrawals;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;

class FilamentWithdrawalsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-withdrawals';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            WithdrawalMethodResource::class,
            WithdrawalRequestResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }
}
