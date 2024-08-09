<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources;

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalMethodFieldsRelation;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalRequestsRelation;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WithdrawalMethodResource extends Resource
{
    protected static ?string $model = WithdrawalMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return trans('filament-withdrawals::messages.group');
    }

    public static function getNavigationLabel(): string
    {
        return trans('filament-withdrawals::messages.withdrawal_methods.title');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_methods.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_methods.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.logo'))
                    ->columnSpanFull()
                    ->collection('logo'),
                Forms\Components\TextInput::make('name')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.name'))
                    ->required(),
                Forms\Components\TextInput::make('min_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.minimum_amount'))
                    ->required(),
                Forms\Components\TextInput::make('max_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.maximum_amount'))
                    ->required(),
                Forms\Components\TextInput::make('currency')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.currency'))
                    ->required(),
                Forms\Components\TextInput::make('rate')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.conversion_rate'))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.description')),
                Forms\Components\Toggle::make('status')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.is_active')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.minimum_amount'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.maximum_amount'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.currency'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.conversion_rate'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Status')
                    ->options([
                        '1' => trans('filament-withdrawals::messages.withdrawal_methods.columns.active'),
                        '0' => trans('filament-withdrawals::messages.withdrawal_methods.columns.inactive'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchable();
    }

    public static function getRelations(): array
    {
        return [
            WithdrawalMethodFieldsRelation::class,
            WithdrawalRequestsRelation::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawalMethods::route('/'),
            'edit' => Pages\EditWithdrawalMethod::route('/{record}/edit'),
        ];
    }
}
