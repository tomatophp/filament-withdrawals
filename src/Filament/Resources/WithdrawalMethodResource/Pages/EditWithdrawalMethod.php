<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource;

class EditWithdrawalMethod extends EditRecord
{
    protected static string $resource = WithdrawalMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
