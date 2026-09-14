<?php

use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;
use TomatoPHP\FilamentWithdrawals\Tests\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->depositFloat(500);
    actingAs($this->user);

    $this->method = WithdrawalMethod::factory()->create([
        'min_amount' => 10,
        'max_amount' => 1000,
        'rate' => 2,
        'currency' => 'EUR',
    ]);
    $this->method->fields()->create([
        'type' => 'text',
        'name' => 'account_number',
        'label' => ['en' => 'Account Number'],
        'is_required' => true,
        'order' => 1,
    ]);
});

it('builds the method fields followed by the amount field', function () {
    $schema = FilamentWithdrawalFormBuilder::make($this->method->id)->build();

    expect(collect($schema)->map->getName()->all())->toBe(['account_number', 'amount'])
        ->and($schema[0]->getLabel())->toBe('Account Number')
        ->and($schema[0]->isRequired())->toBeTrue();
});

it('returns an empty schema for an unknown method', function () {
    expect(FilamentWithdrawalFormBuilder::make(999)->build())->toBe([]);
});

it('creates a pending request for the signed in user', function () {
    $request = FilamentWithdrawalFormBuilder::make($this->method->id)
        ->send(['account_number' => 'EG123', 'amount' => 100]);

    expect($request)->toBeInstanceOf(WithdrawalRequest::class)
        ->and($request->status)->toBe('pending')
        ->and($request->model_type)->toBe($this->user->getMorphClass())
        ->and((int) $request->model_id)->toBe($this->user->id)
        ->and($request->payload)->toBe(['account_number' => 'EG123'])
        ->and($request->currency)->toBe('EUR')
        ->and((float) $request->rate)->toBe(2.0);
});

it('rejects amounts outside the method limits', function (int $amount) {
    expect(FilamentWithdrawalFormBuilder::make($this->method->id)->send(['amount' => $amount]))->toBeNull()
        ->and(WithdrawalRequest::query()->count())->toBe(0);
})->with([5, 5000]);

it('rejects amounts above the wallet balance', function () {
    expect(FilamentWithdrawalFormBuilder::make($this->method->id)->send(['amount' => 900]))->toBeNull()
        ->and(WithdrawalRequest::query()->count())->toBe(0);
});

it('rejects a new request while another one is in progress', function () {
    $builder = FilamentWithdrawalFormBuilder::make($this->method->id);

    expect($builder->send(['amount' => 50]))->not->toBeNull()
        ->and($builder->send(['amount' => 60]))->toBeNull()
        ->and(WithdrawalRequest::query()->count())->toBe(1);
});
