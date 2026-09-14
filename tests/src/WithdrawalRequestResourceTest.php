<?php

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\EditWithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalRequestsRelation;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Concerns\ManagesWithdrawalRequests;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalRequestResource\Pages\ListWithdrawalRequests;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;
use TomatoPHP\FilamentWithdrawals\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());

    $this->owner = User::factory()->create(['name' => 'Farida Ashraf']);
    $this->method = WithdrawalMethod::factory()->create(['name' => 'Bank Transfer']);
    $this->method->fields()->create([
        'type' => 'text',
        'name' => 'account_number',
        'label' => ['en' => 'Account Number'],
        'order' => 1,
    ]);

    $this->makeRequest = fn (array $attributes = []) => WithdrawalRequest::factory()->create([
        'withdrawal_method_id' => $this->method->id,
        'model_type' => $this->owner->getMorphClass(),
        'model_id' => $this->owner->id,
        ...$attributes,
    ]);
});

it('renders the withdrawal requests list', function () {
    $requests = collect([($this->makeRequest)(), ($this->makeRequest)()]);

    $this->get(WithdrawalRequestResource::getUrl('index'))->assertSuccessful();

    livewire(ListWithdrawalRequests::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($requests)
        ->assertSee('Farida Ashraf')
        ->assertSee('Bank Transfer');
});

it('shows a request with its payload labelled by the method fields', function () {
    $request = ($this->makeRequest)(['payload' => ['account_number' => 'EG380019000500000000263180002']]);

    livewire(ListWithdrawalRequests::class)
        ->mountTableAction('view', $request)
        ->assertSuccessful()
        ->assertHasNoErrors();

    // The payload used to be matched on a non-existent `key` column, so labels never resolved.
    $entries = ManagesWithdrawalRequests::payloadEntries($request);

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->getLabel())->toBe('Account Number')
        ->and($entries[0]->getState())->toBe('EG380019000500000000263180002');
});

it('opens the status actions of a request', function () {
    $request = ($this->makeRequest)();

    livewire(ListWithdrawalRequests::class)
        ->mountTableAction('edit', $request)
        ->assertSuccessful()
        ->assertHasNoErrors();
});

it('filters requests by status', function () {
    $pending = ($this->makeRequest)(['status' => 'pending']);
    $completed = ($this->makeRequest)(['status' => 'completed']);

    livewire(ListWithdrawalRequests::class)
        ->filterTable('status', 'completed')
        ->assertCanSeeTableRecords([$completed])
        ->assertCanNotSeeTableRecords([$pending]);
});

it('completes a request and withdraws the amount from the owner wallet', function () {
    $this->owner->depositFloat(300);
    $request = ($this->makeRequest)(['amount' => 120]);

    expect(ManagesWithdrawalRequests::complete($request))->toBeTrue()
        ->and($request->refresh()->status)->toBe('completed')
        ->and((float) $this->owner->refresh()->balanceFloat)->toBe(180.0);
});

it('does not complete a request above the owner wallet balance', function () {
    $this->owner->depositFloat(50);
    $request = ($this->makeRequest)(['amount' => 120]);

    expect(ManagesWithdrawalRequests::complete($request))->toBeFalse()
        ->and($request->refresh()->status)->toBe('pending')
        ->and((float) $this->owner->refresh()->balanceFloat)->toBe(50.0);
});

it('calculates the final amount with the method rate', function () {
    $request = ($this->makeRequest)(['amount' => 100, 'rate' => 48.5, 'currency' => 'EGP']);

    expect(ManagesWithdrawalRequests::finalAmount($request))->toContain('4,850.00');
});

it('renders the requests relation manager on a method', function () {
    $request = ($this->makeRequest)();

    livewire(WithdrawalRequestsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$request]);
});
