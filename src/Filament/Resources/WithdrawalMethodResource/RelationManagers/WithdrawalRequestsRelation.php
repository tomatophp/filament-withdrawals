<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers;

use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use TomatoPHP\FilamentCms\Models\FormRequest;

class WithdrawalRequestsRelation extends RelationManager
{
    protected static string $relationship = 'requests';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }

    /**
     * @return string|null
     */
    public static function getModelLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.single');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return trans('filament-withdrawals::messages.withdrawal_requests.title');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Actions::make([
                    Action::make('process')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.process'))
                        ->icon('heroicon-m-arrow-path')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function (WithdrawalRequest $record) {
                            $record->status = 'processing';
                            $record->save();
                        }),
                    Action::make('completed')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.completed'))
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (WithdrawalRequest $record) {
                            // Retrieve model and wallet
                            $user = $record->model;
                            $wallet = $user->wallet;

                            // Perform withdrawal
                            if ($wallet->balance >= $record->amount) {
                                $wallet->withdraw($record->amount);

                                // Update status
                                $record->status = 'completed';
                                $record->save();

                                // Send success notification
                                Notification::make()
                                    ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.completed.title'))
                                    ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.completed.body'))
                                    ->success()
                                    ->send();
                            } else {
                                // Send error notification
                                Notification::make()
                                    ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.insufficient.title'))
                                    ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.insufficient.body'))
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('cancelled')
                        ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.cancelled'))
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (WithdrawalRequest $record) {
                            // Update status
                            $record->status = 'cancelled';
                            $record->save();

                            // Send success notification
                            Notification::make()
                                ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.cancelled.title'))
                                ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.cancelled.body'))
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('description')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.description'))
                ->columnSpanFull(),
            TextEntry::make('model.username')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.username')),
            TextEntry::make('form.name')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.method_name')),
            TextEntry::make('time')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.time')),
            TextEntry::make('date')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.date')),
            TextEntry::make('amount')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.amount'))
                ->money('USD'),
            TextEntry::make('currency')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.currency'))
                ->money('USD'),
            TextEntry::make('rate')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.rate'))
                ->money('USD'),
            TextEntry::make('amount')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.final_amount'))
                ->formatStateUsing(function (WithdrawalRequest $record) {
                    return Number::currency($record->amount * $record->rate, in: $record->currency);
                })
                ->html(),
            KeyValueEntry::make('payload')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.payload'))
                ->columnSpanFull()
                ->schema(function (WithdrawalRequest $record) {
                    $getEntryText = [];
                    foreach ($record->payload as $key => $value) {
                        $field = $record->form->fields->where('key', $key)->first();
                        $getEntryText[] = TextEntry::make($key)
                            ->label($field->label ?? str($key)->title())
                            ->default($value)
                            ->columnSpanFull();
                    }

                    return $getEntryText;
                })
                ->columns(2)
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.status'))
                    ->badge()
                    ->state(fn ($record) => match ($record->status) {
                        "pending" => trans('filament-withdrawals::messages.forms.requests.columns.pending'),
                        "processing" => trans('filament-withdrawals::messages.forms.requests.columns.processing'),
                        "completed" => trans('filament-withdrawals::messages.forms.requests.columns.completed'),
                        "cancelled" => trans('filament-withdrawals::messages.forms.requests.columns.cancelled'),
                        default => $record->status,
                    })
                    ->icon(fn ($record) => match ($record->status) {
                        'pending' => 'heroicon-s-rectangle-stack',
                        'processing' => 'heroicon-s-arrow-path',
                        'completed' => 'heroicon-s-check-circle',
                        'cancelled' => 'heroicon-s-x-circle',
                        default => 'heroicon-s-x-circle',
                    })
                    ->color(fn ($record) => match ($record->status) {
                        'pending' => 'info',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.description')),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(trans('filament-withdrawals::messages.forms.requests.columns.status'))
                    ->searchable()
                    ->options([
                        "pending" => trans('filament-withdrawals::messages.forms.requests.columns.pending'),
                        "processing" => trans('filament-withdrawals::messages.forms.requests.columns.processing'),
                        "completed" => trans('filament-withdrawals::messages.forms.requests.columns.completed'),
                        "cancelled" => trans('filament-withdrawals::messages.forms.requests.columns.cancelled'),
                    ])
                    ->columnSpanFull(),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('status')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
