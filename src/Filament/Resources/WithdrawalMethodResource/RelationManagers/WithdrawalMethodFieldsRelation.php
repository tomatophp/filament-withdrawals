<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormFields;
use TomatoPHP\FilamentTranslationComponent\Components\Translation;

class WithdrawalMethodFieldsRelation extends RelationManager
{
    protected static string $relationship = 'fields';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }

    /**
     * @return string|null
     */
    public static function getModelLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.single');
    }


    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.forms.fields.title');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.general'))
                            ->icon('heroicon-s-information-circle')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.type'))
                                    ->searchable()
                                    ->options(FilamentWithdrawalFormFields::getOptions()->pluck('label', 'name')->toArray())
                                    ->default('text'),
                                Forms\Components\TextInput::make('name')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.name'))
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                                        if (str($state)->contains('email')) {
                                            $set('type', 'email');
                                        }
                                        if (str($state)->contains('phone')) {
                                            $set('type', 'tel');
                                        }
                                        if (str($state)->contains(['is_', 'has_'])) {
                                            $set('type', 'toggle');
                                        }
                                        if (str($state)->contains(['at', 'date'])) {
                                            $set('type', 'date');
                                        }
                                        if (str($state)->contains('password')) {
                                            $set('type', 'password');
                                        }
                                        if (str($state)->contains(['description', 'message'])) {
                                            $set('type', 'textarea');
                                        }
                                        if (str($state)->contains(['body', 'about'])) {
                                            $set('type', 'rich');
                                        }
                                        if (str($state)->contains('price')) {
                                            $set('type', 'number');
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
                        Forms\Components\Tabs\Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.relation'))
                            ->icon('heroicon-s-squares-plus')
                            ->schema([
                                Forms\Components\Toggle::make('is_relation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_relation'))
                                    ->columnSpanFull()
                                    ->live(),
                                Forms\Components\TextInput::make('relation_name')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.relation_name'))
                                    ->hidden(fn (Forms\Get $get) => !$get('is_relation'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('relation_column')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.relation_column'))
                                    ->hidden(fn (Forms\Get $get) => !$get('is_relation'))
                                    ->maxLength(255),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.options'))
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
                                    ->hidden(fn (Forms\Get $get) => !$get('has_options')),
                            ]),
                        Forms\Components\Tabs\Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.labels'))
                            ->icon('heroicon-s-language')
                            ->schema([
                                Translation::make('label')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.label')),
                                Translation::make('placeholder')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.placeholder')),
                                Translation::make('hint')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.hint')),
                            ]),
                        Forms\Components\Tabs\Tab::make(trans('filament-withdrawals::messages.forms.fields.tabs.validation'))
                            ->icon('heroicon-s-variable')
                            ->schema([
                                Forms\Components\Toggle::make('is_required')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_required'))
                                    ->live(),
                                Translation::make('required_message')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.required_message'))
                                    ->hidden(fn (Forms\Get $get) => !$get('is_required')),
                                Forms\Components\Toggle::make('has_validation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.has_validation'))
                                    ->live(),
                                Forms\Components\Repeater::make('validation')
                                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.validation'))
                                    ->schema([
                                        Forms\Components\TextInput::make('rule')->label(trans('filament-withdrawals::messages.forms.fields.columns.rule')),
                                        Translation::make('message')->label(trans('filament-withdrawals::messages.forms.fields.columns.message')),
                                    ])
                                    ->hidden(fn (Forms\Get $get) => !$get('has_validation')),
                            ])
                    ])
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-s-plus-circle')
                    ->after(function (array $data, $record) {
                        $record->name = Str::of($record->name)->replace(' ', '_')->lower()->toString();
                        $record->save();
                    }),
                Tables\Actions\Action::make('preview')
                    ->label(trans('filament-withdrawals::messages.forms.fields.actions.preview'))
                    ->icon('heroicon-s-eye')
                    ->color('info')
                    ->form(function () {
                        return FilamentWithdrawalFormBuilder::make($this->getOwnerRecord()->id)->build();
                    })->action(function (array $data) {
                        FilamentWithdrawalFormBuilder::make($this->getOwnerRecord()->id)->send($data);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()->after(function (array $data, $record) {
                    $record->name = Str::of($record->name)->replace(' ', '_')->lower()->toString();
                    $record->save();
                }),
                Tables\Actions\DeleteAction::make()
            ])
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.type'))
                    ->badge()
                    ->icon(fn ($record) => optional(FilamentWithdrawalFormFields::getOptions()->where('name', $record->type)->first())->icon)
                    ->color(fn ($record) => optional(FilamentWithdrawalFormFields::getOptions()->where('name', $record->type)->first())->color)
                    ->state(fn ($record) => optional(FilamentWithdrawalFormFields::getOptions()->where('name', $record->type)->first())->label)
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.name'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_required')
                    ->label(trans('filament-withdrawals::messages.forms.fields.columns.is_required'))
                    ->searchable(),
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
                Tables\Grouping\Group::make('group')
            ])
            ->defaultSort('created_at')
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
            ])
            ->reorderable('order');
    }
}
