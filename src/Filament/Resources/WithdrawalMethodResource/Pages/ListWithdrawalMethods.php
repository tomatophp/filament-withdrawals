<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages;

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalMethods extends ListRecords
{
    protected static string $resource = WithdrawalMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
