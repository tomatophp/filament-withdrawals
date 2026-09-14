<?php

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\CreateWithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\EditWithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\ListWithdrawalMethods;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the list, create and edit pages', function () {
    $method = WithdrawalMethod::factory()->create();

    $this->get(WithdrawalMethodResource::getUrl('index'))->assertSuccessful();
    $this->get(WithdrawalMethodResource::getUrl('create'))->assertSuccessful();
    $this->get(WithdrawalMethodResource::getUrl('edit', ['record' => $method]))->assertSuccessful();
});

it('lists withdrawal methods', function () {
    $methods = WithdrawalMethod::factory()->count(3)->create();

    livewire(ListWithdrawalMethods::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($methods);
});

it('filters withdrawal methods by status', function () {
    $active = WithdrawalMethod::factory()->create(['status' => true]);
    $inactive = WithdrawalMethod::factory()->create(['status' => false]);

    livewire(ListWithdrawalMethods::class)
        ->filterTable('status', true)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

it('creates a withdrawal method', function () {
    livewire(CreateWithdrawalMethod::class)
        ->fillForm([
            'name' => 'Bank Transfer',
            'currency' => 'EGP',
            'min_amount' => 50,
            'max_amount' => 1000,
            'rate' => 48.5,
            'status' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $method = WithdrawalMethod::query()->where('name', 'Bank Transfer')->firstOrFail();

    expect($method->currency)->toBe('EGP')
        ->and($method->status)->toBeTrue()
        ->and((float) $method->rate)->toBe(48.5);
});

it('validates the withdrawal method form', function () {
    livewire(CreateWithdrawalMethod::class)
        ->fillForm([
            'name' => null,
            'min_amount' => 100,
            'max_amount' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'max_amount']);
});

it('edits a withdrawal method', function () {
    $method = WithdrawalMethod::factory()->create(['name' => 'PayPal']);

    livewire(EditWithdrawalMethod::class, ['record' => $method->getRouteKey()])
        ->assertSchemaStateSet(['name' => 'PayPal'])
        ->fillForm(['name' => 'Payoneer', 'max_amount' => 9000])
        ->call('save')
        ->assertHasNoFormErrors();

    $method->refresh();

    expect($method->name)->toBe('Payoneer')
        ->and((float) $method->max_amount)->toBe(9000.0);
});

it('deletes a withdrawal method from the table', function () {
    $method = WithdrawalMethod::factory()->create();

    livewire(ListWithdrawalMethods::class)
        ->callTableAction('delete', $method);

    expect(WithdrawalMethod::query()->find($method->id))->toBeNull();
});
