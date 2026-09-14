<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethodOption;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormFields;

class WithdrawalMethodFieldsRelation extends RelationManager
{
    protected static string $relationship = 'fields';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }

    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }

    public static function getModelLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.single');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.general'))
                            ->icon('heroicon-s-information-circle')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.type'))
                                    ->searchable()
                                    ->options(FilamentWithdrawalFormFields::getOptions()->pluck('label', 'name')->toArray())
                                    ->default('text'),
                                Forms\Components\TextInput::make('name')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.name'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        if ($type = static::guessType((string) $state)) {
                                            $set('type', $type);
                                        }
                                    })
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('group')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.group'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('default')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.default')),
                            ])->columns(2),
                        Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.relation'))
                            ->icon('heroicon-s-squares-plus')
                            ->schema([
                                Forms\Components\Toggle::make('is_relation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_relation'))
                                    ->columnSpanFull()
                                    ->live(),
                                Forms\Components\TextInput::make('relation_name')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.relation_name'))
                                    ->hidden(fn (Get $get): bool => ! $get('is_relation'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('relation_column')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.relation_column'))
                                    ->hidden(fn (Get $get): bool => ! $get('is_relation'))
                                    ->maxLength(255),
                            ])->columns(2),
                        Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.options'))
                            ->icon('heroicon-s-rectangle-group')
                            ->schema([
                                Forms\Components\Toggle::make('is_multi')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_multi')),
                                Forms\Components\Toggle::make('has_options')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.has_options'))
                                    ->live(),
                                Forms\Components\Repeater::make('options')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.options'))
                                    ->schema([
                                        Translation::make('label')->label(trans('filament-withdrawals::messages.forms.fields.columns.label')),
                                        Forms\Components\TextInput::make('value')->label(trans('filament-withdrawals::messages.forms.fields.columns.value')),
                                    ])
                                    ->hidden(fn (Get $get): bool => ! $get('has_options')),
                            ]),
                        Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.labels'))
                            ->icon('heroicon-s-language')
                            ->schema([
                                Translation::make('label')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.label')),
                                Translation::make('placeholder')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.placeholder')),
                                Translation::make('hint')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.hint')),
                            ]),
                        Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.validation'))
                            ->icon('heroicon-s-variable')
                            ->schema([
                                Forms\Components\Toggle::make('is_required')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_required'))
                                    ->live(),
                                Translation::make('required_message')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.required_message'))
                                    ->hidden(fn (Get $get): bool => ! $get('is_required')),
                                Forms\Components\Toggle::make('has_validation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.has_validation'))
                                    ->live(),
                                Forms\Components\Repeater::make('validation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.validation'))
                                    ->schema([
                                        Forms\Components\TextInput::make('rule')->label(trans('filament-withdrawals::messages.forms.fields.columns.rule')),
                                        Translation::make('message')->label(trans('filament-withdrawals::messages.forms.fields.columns.message')),
                                    ])
                                    ->hidden(fn (Get $get): bool => ! $get('has_validation')),
                            ]),
                    ]),
            ])->columns(1);
    }

    /**
     * Suggest a field type from the field name (email, phone, is_active, created_at, ...).
     */
    public static function guessType(string $name): ?string
    {
        $name = Str::lower($name);

        return match (true) {
            str_contains($name, 'email') => 'email',
            str_contains($name, 'phone') => 'tel',
            str_contains($name, 'password') => 'password',
            str_starts_with($name, 'is_') || str_starts_with($name, 'has_') => 'toggle',
            str_ends_with($name, '_at') || str_contains($name, 'date') => 'date',
            str_contains($name, 'description') || str_contains($name, 'message') => 'textarea',
            str_contains($name, 'body') || str_contains($name, 'about') => 'rich',
            str_contains($name, 'price') || str_contains($name, 'amount') => 'number',
            default => null,
        };
    }

    public static function normalizeName(WithdrawalMethodOption $record): void
    {
        $record->name = Str::of($record->name)->replace(' ', '_')->lower()->toString();
        $record->save();
    }

    public function table(Table $table): Table
    {
        $type = fn (WithdrawalMethodOption $record) => FilamentWithdrawalFormFields::getOptions()->firstWhere('name', $record->type);

        return $table
            ->headerActions([
                Actions\CreateAction::make()
                    ->icon('heroicon-s-plus-circle')
                    ->after(fn (WithdrawalMethodOption $record) => static::normalizeName($record)),
                Actions\Action::make('preview')
                    ->label(trans('filament-withdrawals::messages.forms.fields.actions.preview'))
                    ->icon('heroicon-s-eye')
                    ->color('info')
                    ->schema(fn (): array => FilamentWithdrawalFormBuilder::make($this->getOwnerRecord()->getKey())->build())
                    ->action(fn (array $data) => FilamentWithdrawalFormBuilder::make($this->getOwnerRecord()->getKey())->send($data)),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->after(fn (WithdrawalMethodOption $record) => static::normalizeName($record)),
                Actions\DeleteAction::make(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.type'))
                    ->badge()
                    ->icon(fn (WithdrawalMethodOption $record) => $type($record)?->icon)
                    ->color(fn (WithdrawalMethodOption $record) => $type($record)?->color ?? 'primary')
                    ->formatStateUsing(fn (WithdrawalMethodOption $record) => $type($record)?->label ?? $record->type)
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.name'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_required')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_required')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('group'),
            ])
            ->defaultSort('order')
            ->toolbarActions([
                Actions\DeleteBulkAction::make(),
            ])
            ->reorderable('order');
    }
}
