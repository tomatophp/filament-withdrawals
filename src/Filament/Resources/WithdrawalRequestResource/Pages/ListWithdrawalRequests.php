<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Pages;

use Filament\Resources\Pages\ListRecords;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;

class ListWithdrawalRequests extends ListRecords
{
    protected static string $resource = WithdrawalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
