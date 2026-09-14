<?php

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\EditWithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalMethodFieldsRelation;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;
use TomatoPHP\FilamentWithdrawals\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());

    $this->method = WithdrawalMethod::factory()->create();
});

it('renders the fields relation manager', function () {
    $this->method->fields()->create(['type' => 'text', 'name' => 'account_number', 'order' => 1]);

    livewire(WithdrawalMethodFieldsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($this->method->fields);
});

it('creates a field and normalizes its name', function () {
    livewire(WithdrawalMethodFieldsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->callTableAction('create', data: [
            'type' => 'text',
            'name' => 'Account Number',
        ])
        ->assertHasNoTableActionErrors();

    expect($this->method->fields()->first()->name)->toBe('account_number');
});

it('guesses the field type from the field name', function (string $name, ?string $type) {
    expect(WithdrawalMethodFieldsRelation::guessType($name))->toBe($type);
})->with([
    ['customer_email', 'email'],
    ['phone', 'tel'],
    ['is_primary', 'toggle'],
    ['paid_at', 'date'],
    ['notes_message', 'textarea'],
    ['max_amount', 'number'],
    // "rate" used to be detected as a date because it contains "at".
    ['rate', null],
    ['iban', null],
]);

it('builds the preview form from the method fields', function () {
    $this->method->fields()->create(['type' => 'text', 'name' => 'account_number', 'order' => 1]);
    $this->method->fields()->create(['type' => 'email', 'name' => 'paypal_email', 'order' => 2]);

    $schema = FilamentWithdrawalFormBuilder::make($this->method->id)->build();

    expect(collect($schema)->map->getName()->all())->toBe(['account_number', 'paypal_email', 'amount']);
});
