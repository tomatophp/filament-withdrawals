<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;

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
