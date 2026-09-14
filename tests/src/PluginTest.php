<?php

use Filament\Facades\Filament;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;
use TomatoPHP\FilamentWithdrawals\FilamentWithdrawalsPlugin;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalFormFieldType;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormFields;

it('registers the plugin and its resources on the panel', function () {
    $panel = Filament::getPanel('admin');

    expect($panel->getPlugin('filament-withdrawals'))->toBeInstanceOf(FilamentWithdrawalsPlugin::class)
        ->and($panel->getResources())->toContain(WithdrawalMethodResource::class, WithdrawalRequestResource::class);
});

it('registers the built in form field types once', function () {
    $names = FilamentWithdrawalFormFields::getOptions()->pluck('name');

    expect($names)->toContain('text', 'select', 'email', 'number', 'repeater')
        ->and($names->duplicates())->toBeEmpty();
});

it('replaces a field type registered again with the same name', function () {
    FilamentWithdrawalFormFields::register(WithdrawalFormFieldType::make('text')->label('Plain Text'));

    expect(FilamentWithdrawalFormFields::getOptions()->where('name', 'text'))->toHaveCount(1)
        ->and(FilamentWithdrawalFormFields::getOptions()->firstWhere('name', 'text')->label)->toBe('Plain Text');
});
