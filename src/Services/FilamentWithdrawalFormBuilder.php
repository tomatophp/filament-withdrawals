<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;

class FilamentWithdrawalFormBuilder
{
    public string $id;

    public ?WithdrawalMethod $form = null;

    final public function __construct() {}

    public static function make(int | string $id): static
    {
        return (new static)->key((string) $id);
    }

    public function key(string $id): static
    {
        $this->id = $id;
        $this->form = WithdrawalMethod::query()->find((int) $this->id);

        return $this;
    }

    public function build(): array
    {
        $schema = [];
        $form = $this->form;

        if (! $form) {
            return $schema;
        }

        foreach ($form->fields()->get() as $field) {
            $fieldType = FilamentWithdrawalFormFields::getOptions()->where('name', $field->type)->first();

            if (! $fieldType) {
                continue;
            }

            $messages = [];
            $fieldBuild = $fieldType->className::make($field->name);

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
                $messages['required'] = $field->required_message ?: null;
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
                $fieldBuild->options(collect($field->options)->map(function (array $item): array {
                    $item['label'] = is_array($item['label'] ?? null)
                        ? ($item['label'][app()->getLocale()] ?? null)
                        : ($item['label'] ?? null);

                    return $item;
                })->pluck('label', 'value')->toArray());
            }
            if ($field->has_validation) {
                $rules = [];
                foreach ($field->validation ?? [] as $rule) {
                    $messages[$rule['rule']] = is_array($rule['message'] ?? null)
                        ? ($rule['message'][app()->getLocale()] ?? null)
                        : ($rule['message'] ?? null);
                    $rules[] = $rule['rule'];
                }
                $fieldBuild->rules($rules);
            }
            if ($messages = array_filter($messages)) {
                $fieldBuild->validationMessages($messages);
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

        // Every withdrawal form asks for the amount.
        $schema[] = TextInput::make('amount')
            ->label(trans('filament-withdrawals::messages.forms.requests.columns.amount'))
            ->numeric()
            ->default(0.00)
            ->minValue(1)
            ->required();

        return $schema;
    }

    /**
     * The signed-in owner of the request: an account on the accounts guard when it is configured
     * and signed in (tomatophp/filament-accounts), otherwise the default guard's user.
     */
    public function owner(): ?Model
    {
        $guard = config('filament-withdrawals.accounts.guard');

        if ($guard && array_key_exists($guard, config('auth.guards', [])) && auth($guard)->check()) {
            return auth($guard)->user();
        }

        return auth()->user();
    }

    public function send(array $data): ?WithdrawalRequest
    {
        if (empty($data) || ! $this->form) {
            Notification::make()
                ->title(trans('filament-withdrawals::messages.forms.fields.actions.preview'))
                ->body('Form Empty!')
                ->danger()
                ->send();

            return null;
        }

        $amount = (float) ($data['amount'] ?? 0);

        if ($amount < (float) $this->form->min_amount) {
            return $this->fail('amount_too_low');
        }

        if ($amount > (float) $this->form->max_amount) {
            return $this->fail('amount_too_high');
        }

        $owner = $this->owner();

        if (! $owner) {
            return $this->fail('balance_is_not_enough');
        }

        $wallet = $owner->wallet ?? null;

        if ($wallet && $amount > $wallet->balanceFloatNum) {
            return $this->fail('balance_is_not_enough');
        }

        $inProgress = WithdrawalRequest::query()
            ->where('model_type', $owner->getMorphClass())
            ->where('model_id', $owner->getKey())
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($inProgress) {
            return $this->fail('request_in_progress');
        }

        unset($data['amount']);

        $withdrawalRequest = WithdrawalRequest::query()->create([
            'model_type' => $owner->getMorphClass(),
            'model_id' => $owner->getKey(),
            'withdrawal_method_id' => $this->form->id,
            'status' => 'pending',
            'payload' => $data,
            'amount' => $amount,
            'rate' => $this->form->rate,
            'currency' => $this->form->currency,
            'description' => 'Created From Form Preview',
            'date' => Carbon::now()->toDateString(),
            'time' => Carbon::now()->toTimeString(),
        ]);

        Notification::make()
            ->title(trans('filament-withdrawals::messages.forms.fields.actions.preview'))
            ->body('Form Preview has been created successfully')
            ->success()
            ->send();

        return $withdrawalRequest;
    }

    protected function fail(string $notification): null
    {
        Notification::make()
            ->title(trans("filament-withdrawals::messages.withdrawal_requests.notification.{$notification}.title"))
            ->body(trans("filament-withdrawals::messages.withdrawal_requests.notification.{$notification}.body"))
            ->danger()
            ->send();

        return null;
    }
}
