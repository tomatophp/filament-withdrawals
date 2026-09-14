<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers;

use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Concerns\ManagesWithdrawalRequests;

class WithdrawalRequestsRelation extends RelationManager
{
    protected static string $relationship = 'requests';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    public static function getModelLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.single');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(ManagesWithdrawalRequests::statusActions());
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components(ManagesWithdrawalRequests::infolist())->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ManagesWithdrawalRequests::statusColumn(),
                Tables\Columns\TextColumn::make('model.name')
                    ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.account_name')),
                Tables\Columns\TextColumn::make('amount')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.amount'))
                    ->money(config('filament-withdrawals.currency', 'USD'))
                    ->sortable(),
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
            ])
            ->filters([
                ManagesWithdrawalRequests::statusFilter(),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('status'),
            ])
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
}
