<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Pages;

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawalRequest extends EditRecord
{
    protected static string $resource = WithdrawalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
