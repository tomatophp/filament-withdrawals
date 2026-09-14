<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources;

use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Concerns\ManagesWithdrawalRequests;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Pages;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;

class WithdrawalRequestResource extends Resource
{
    protected static ?string $model = WithdrawalRequest::class;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-s-inbox-stack';

    protected static ?int $navigationSort = 5;

    /**
     * tomatophp/filament-accounts is optional; its account column is used when it is installed.
     */
    protected const ACCOUNT_COLUMN = 'TomatoPHP\\FilamentAccounts\\Components\\AccountColumn';

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-withdrawals::messages.group');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.single');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ManagesWithdrawalRequests::statusActions());
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components(ManagesWithdrawalRequests::infolist())->columns(2);
    }

    public static function table(Table $table): Table
    {
        $accountColumn = static::ACCOUNT_COLUMN;
        $hasAccounts = class_exists($accountColumn);

        return $table
            ->columns(array_values(array_filter([
                $hasAccounts
                    ? $accountColumn::make('model.id')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.account_id'))
                    : Tables\Columns\TextColumn::make('model.name')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.account_name')),
                $hasAccounts
                    ? Tables\Columns\TextColumn::make('model.teams.name')
                        ->badge()
                        ->icon('heroicon-o-user-group')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.teams'))
                        ->toggleable()
                    : null,
                Tables\Columns\TextColumn::make('form.name')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.method_name')),
                Tables\Columns\TextColumn::make('amount')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.amount'))
                    ->money(config('filament-withdrawals.currency', 'USD'))
                    ->sortable(),
                ManagesWithdrawalRequests::statusColumn(),
                Tables\Columns\TextColumn::make('description')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.description'))
                    ->limit(40),
                Tables\Columns\TextColumn::make('date')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.time')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])))
            ->filters([
                ManagesWithdrawalRequests::statusFilter(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawalRequests::route('/'),
        ];
    }
}
