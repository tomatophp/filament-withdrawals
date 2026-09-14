<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalMethodFieldsRelation;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalRequestsRelation;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;

class WithdrawalMethodResource extends Resource
{
    protected static ?string $model = WithdrawalMethod::class;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-credit-card';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.logo'))
                    ->columnSpanFull()
                    ->image()
                    ->visibility('public')
                    ->collection('logo'),
                Forms\Components\TextInput::make('name')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.currency'))
                    ->default(config('filament-withdrawals.currency', 'USD'))
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('min_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.minimum_amount'))
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('max_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.maximum_amount'))
                    ->numeric()
                    ->gte('min_amount')
                    ->required(),
                Forms\Components\TextInput::make('rate')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.conversion_rate'))
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.is_active'))
                    ->inline(false),
                Forms\Components\Textarea::make('description')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency = config('filament-withdrawals.currency', 'USD');

        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.logo'))
                    ->collection('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.minimum_amount'))
                    ->money($currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_amount')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.maximum_amount'))
                    ->money($currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.currency'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.conversion_rate'))
                    ->numeric(4)
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label(trans('filament-withdrawals::messages.withdrawal_methods.columns.is_active'))
                    ->trueLabel(trans('filament-withdrawals::messages.withdrawal_methods.columns.active'))
                    ->falseLabel(trans('filament-withdrawals::messages.withdrawal_methods.columns.inactive')),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'create' => Pages\CreateWithdrawalMethod::route('/create'),
            'edit' => Pages\EditWithdrawalMethod::route('/{record}/edit'),
        ];
    }
}
