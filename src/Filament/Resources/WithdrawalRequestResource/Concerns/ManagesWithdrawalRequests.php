<?php

namespace TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Concerns;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Illuminate\Support\Number;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;

/**
 * Form (status actions), infolist and table shared by the withdrawal requests resource
 * and the requests relation manager on a withdrawal method.
 */
class ManagesWithdrawalRequests
{
    public static function statusActions(): array
    {
        return [
            Actions::make([
                Action::make('process')
                    ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.process'))
                    ->icon('heroicon-m-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (WithdrawalRequest $record): bool => $record->status === 'pending')
                    ->action(function (WithdrawalRequest $record): void {
                        $record->status = 'processing';
                        $record->save();
                    }),
                Action::make('completed')
                    ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.completed'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (WithdrawalRequest $record): bool => in_array($record->status, ['pending', 'processing'], true))
                    ->action(fn (WithdrawalRequest $record) => static::complete($record)),
                Action::make('cancelled')
                    ->label(trans('filament-withdrawals::messages.withdrawal_requests.columns.cancelled'))
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (WithdrawalRequest $record): bool => in_array($record->status, ['pending', 'processing'], true))
                    ->action(function (WithdrawalRequest $record): void {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()
                            ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.cancelled.title'))
                            ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.cancelled.body'))
                            ->success()
                            ->send();
                    }),
            ]),
        ];
    }

    /**
     * Withdraw the requested amount from the owner's wallet and mark the request completed.
     */
    public static function complete(WithdrawalRequest $record): bool
    {
        $wallet = $record->model?->wallet;

        if (! $wallet || $wallet->balanceFloatNum < (float) $record->amount) {
            Notification::make()
                ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.insufficient.title'))
                ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.insufficient.body'))
                ->danger()
                ->send();

            return false;
        }

        $wallet->withdrawFloat($record->amount, ['withdrawal_request_id' => $record->id]);

        $record->status = 'completed';
        $record->save();

        Notification::make()
            ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.completed.title'))
            ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.completed.body'))
            ->success()
            ->send();

        return true;
    }

    public static function infolist(): array
    {
        $currency = config('filament-withdrawals.currency', 'USD');

        return [
            TextEntry::make('description')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.description'))
                ->columnSpanFull(),
            TextEntry::make('model.name')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.username')),
            TextEntry::make('form.name')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.method_name')),
            TextEntry::make('date')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.date'))
                ->date(),
            TextEntry::make('time')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.time')),
            TextEntry::make('amount')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.amount'))
                ->money($currency),
            TextEntry::make('currency')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.currency')),
            TextEntry::make('rate')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.rate'))
                ->numeric(4),
            TextEntry::make('final_amount')
                ->label(trans('filament-withdrawals::messages.forms.requests.columns.final_amount'))
                ->state(fn (WithdrawalRequest $record): string => static::finalAmount($record)),
            Section::make(trans('filament-withdrawals::messages.forms.requests.columns.payload'))
                ->columnSpanFull()
                ->columns(2)
                ->visible(fn (WithdrawalRequest $record): bool => filled($record->payload))
                ->schema(fn (WithdrawalRequest $record): array => static::payloadEntries($record)),
        ];
    }

    public static function finalAmount(WithdrawalRequest $record): string
    {
        $amount = (float) $record->amount * (float) $record->rate;

        return Number::currency($amount, in: $record->currency ?: config('filament-withdrawals.currency', 'USD')) ?: (string) $amount;
    }

    /**
     * One entry per submitted payload value, labelled with the method field it came from.
     */
    public static function payloadEntries(WithdrawalRequest $record): array
    {
        $fields = $record->form?->fields ?? collect();
        $entries = [];

        foreach ($record->payload ?? [] as $key => $value) {
            $field = $fields->firstWhere('name', $key);

            $entries[] = TextEntry::make("payload.{$key}")
                ->label($field?->label ?: str($key)->headline()->toString())
                ->state(is_array($value) ? implode(', ', $value) : $value);
        }

        return $entries;
    }

    public static function statusColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('status')
            ->label(trans('filament-withdrawals::messages.forms.requests.columns.status'))
            ->badge()
            ->formatStateUsing(fn (?string $state): string => static::statusOptions()[$state] ?? (string) $state)
            ->icon(fn (?string $state): string => match ($state) {
                'pending' => 'heroicon-s-rectangle-stack',
                'processing' => 'heroicon-s-arrow-path',
                'completed' => 'heroicon-s-check-circle',
                default => 'heroicon-s-x-circle',
            })
            ->color(fn (?string $state): string => match ($state) {
                'pending' => 'info',
                'processing' => 'warning',
                'completed' => 'success',
                'cancelled' => 'danger',
                default => 'gray',
            })
            ->searchable();
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => trans('filament-withdrawals::messages.forms.requests.columns.pending'),
            'processing' => trans('filament-withdrawals::messages.forms.requests.columns.processing'),
            'completed' => trans('filament-withdrawals::messages.forms.requests.columns.completed'),
            'cancelled' => trans('filament-withdrawals::messages.forms.requests.columns.cancelled'),
        ];
    }

    public static function statusFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('status')
            ->label(trans('filament-withdrawals::messages.forms.requests.columns.status'))
            ->options(static::statusOptions());
    }
}
