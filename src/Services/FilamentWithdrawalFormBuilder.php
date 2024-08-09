<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;
use Filament\Forms\Components\TextInput;

class FilamentWithdrawalFormBuilder
{
    public string $id;
    public WithdrawalMethod $form;

    public static function make(string $id): static
    {
        return (new static)->key($id);
    }

    public function key(string $id): static
    {
        $this->id = $id;
        $this->form = WithdrawalMethod::query()->where('id', (int)$this->id)->first();
        return $this;
    }

    public function build(): array
    {
        $schema = [];
        $form = $this->form;
        if ($form) {
            $fields = $form->fields()->orderBy('order')->get();
            foreach ($fields as $id => $field) {
                $getFiledBuilder = FilamentWithdrawalFormFields::getOptions()->where('name', $field->type)->first();
                if ($getFiledBuilder) {
                    $messages = [];
                    $title = Str::of($field->name)->title()->toString();
                    $fieldBuild = $getFiledBuilder->className::make($field->name);
                    if ($field->label) {
                        $fieldBuild->label($field->label);
                    }
                    if ($field->hint) {
                        $fieldBuild->hint($field->hint);
                    }
                    if ($field->placeholder) {
                        $fieldBuild->placeholder($field->placeholder);
                    }
                    if ($field->is_required) {
                        $fieldBuild->required();
                        $messages['required'] = $field->required_message[app()->getLocale()] ?? null;
                    }
                    if ($field->default) {
                        $fieldBuild->default($field->default);
                    }
                    if ($field->is_multi) {
                        $fieldBuild->multiple();
                    }
                    if ($field->type === 'number') {
                        $fieldBuild->numeric();
                    }
                    if ($field->type === 'email') {
                        $fieldBuild->email();
                    }
                    if ($field->type === 'tel') {
                        $fieldBuild->tel();
                    }
                    if ($field->type === 'url') {
                        $fieldBuild->url();
                    }
                    if ($field->type === 'password') {
                        $fieldBuild->password();
                    }
                    if ($field->has_options) {
                        $fieldBuild->options(collect($field->options)->map(function ($item) {
                            $item['label'] = $item['label'][app()->getLocale()] ?? null;
                            return $item;
                        })->pluck('label', 'value')->toArray());
                    }
                    if ($field->has_validation) {
                        $rules = [];
                        foreach ($field->validation as $rule) {
                            $messages[$rule['rule']] = $rule['message'][app()->getLocale()] ?? null;
                            $rules[] = $rule['rule'];
                        }
                        $fieldBuild->rules($rules);
                    }
                    if (count($messages)) {
                        $fieldBuild->validationMessages($messages);
                    }
                    if ($field->sub_form) {
                        $fieldBuild->schema(static::make($field->sub_form)->build());
                    }
                    if ($field->is_relation) {
                        $fieldBuild->searchable();
                        if (str($field->relation_name)->contains('\\')) {
                            $fieldBuild->options($field->relation_name::all()->pluck($field->relation_column, 'id')->toArray());
                        } else {
                            $fieldBuild->relationship($field->relation_name, $field->relation_column);
                        }
                    }
                    $schema[] = $fieldBuild;
                }
            }

            // Ensure 'amount' field is always present
            $amountFieldBuild = TextInput::make('amount');
            $amountFieldBuild->label('Amount');
            $amountFieldBuild->numeric();
            $amountFieldBuild->default(0.00);
            $amountFieldBuild->minValue(1);
            $amountFieldBuild->required();

            $schema[] = $amountFieldBuild;
        }

        return $schema;
    }

    public function send(array $data): void
    {
        // Handle empty data case
        if (empty($data)) {
            Notification::make()
                ->title('Form Preview')
                ->body('Form Empty!')
                ->danger()
                ->send();
            return;
        }

        $amount = $data['amount'];

        // Check amount against form limits
        if ($amount < $this->form->min_amount) {
            Notification::make()
                ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.amount_too_low.title'))
                ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.amount_too_low.body'))
                ->danger()
                ->send();
            return;
        }

        if ($amount > $this->form->max_amount) {
            Notification::make()
                ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.amount_too_high.title'))
                ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.amount_too_high.body'))
                ->danger()
                ->send();
            return;
        }

        // Check if user is authenticated and validate wallet balance
        if (auth('accounts')->check()) {
            $user = auth('accounts')->user();
            $wallet = $user->getWallet();

            if ($amount > $wallet->balanceFloatNum) {
                Notification::make()
                    ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.balance_is_not_enough.title'))
                    ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.balance_is_not_enough.body'))
                    ->danger()
                    ->send();
                return;
            }

            $modelType = Account::class;
            $modelId = auth('accounts')->id();
        } else {
            $modelType = User::class;
            $modelId = auth()->id();
        }

        // Check if there are any pending or processing withdrawal requests
        $existingRequest = WithdrawalRequest::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($existingRequest) {
            Notification::make()
                ->title(trans('filament-withdrawals::messages.withdrawal_requests.notification.request_in_progress.title'))
                ->body(trans('filament-withdrawals::messages.withdrawal_requests.notification.request_in_progress.body'))
                ->danger()
                ->send();
            return;
        }

        // Remove 'amount' from $data array
        unset($data['amount']);

        // Create and save withdrawal request
        $withdrawalRequest = new WithdrawalRequest();
        $withdrawalRequest->model_type = $modelType;
        $withdrawalRequest->model_id = $modelId;
        $withdrawalRequest->withdrawal_method_id = $this->form->id;
        $withdrawalRequest->status = 'pending';
        $withdrawalRequest->payload = $data;
        $withdrawalRequest->amount = $amount;
        $withdrawalRequest->rate = $this->form->rate;
        $withdrawalRequest->currency = $this->form->currency;
        $withdrawalRequest->description = "Created From Form Preview";
        $withdrawalRequest->date = Carbon::now()->toDateString();
        $withdrawalRequest->time = Carbon::now()->toTimeString();
        $withdrawalRequest->save();

        // Send success notification
        Notification::make()
            ->title('Form Preview')
            ->body('Form Preview has been created successfully')
            ->success()
            ->send();
    }
}
