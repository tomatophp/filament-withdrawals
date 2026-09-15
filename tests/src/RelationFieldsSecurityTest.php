<?php

use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\Pages\EditWithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Filament\Resources\WithdrawalMethodResource\RelationManagers\WithdrawalMethodFieldsRelation;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalRelations;
use TomatoPHP\FilamentWithdrawals\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['name' => 'Admin User']);
    actingAs($this->admin);

    $this->method = WithdrawalMethod::factory()->create();

    $this->relationField = function (string $relation, string $column): array {
        $this->method->fields()->create([
            'type' => 'select',
            'name' => 'beneficiary',
            'is_relation' => true,
            'relation_name' => $relation,
            'relation_column' => $column,
            'order' => 1,
        ]);

        $field = collect(FilamentWithdrawalFormBuilder::make($this->method->id)->build())
            ->first(fn ($component) => $component->getName() === 'beneficiary');

        return $field->getOptions();
    };
});

// Security: a relation field pointing at the user model and its password column put the password
// hashes of every user into the withdrawal request form.
it('never exposes password hashes through a relation field pointing at the user model', function () {
    $options = ($this->relationField)(User::class, 'password');

    expect(collect($options)->flatten()->all())->not->toContain($this->admin->getAuthPassword())
        ->and($options)->toBe([]);
});

it('never calls a class that is not in the allowlist', function () {
    $options = ($this->relationField)('App\\Models\\DoesNotExist', 'name');

    expect($options)->toBe([]);
});

it('shows the options of an allowlisted model column', function () {
    config()->set('filament-withdrawals.relations', [User::class => ['name']]);

    $options = ($this->relationField)(User::class, 'name');

    expect($options)->toContain('Admin User')
        ->and($options[$this->admin->id])->toBe('Admin User');
});

it('rejects a hidden attribute even when it is allowlisted', function () {
    config()->set('filament-withdrawals.relations', [User::class => ['name', 'password', 'remember_token']]);

    $options = ($this->relationField)(User::class, 'password');

    expect($options)->toBe([])
        ->and(FilamentWithdrawalRelations::columnsFor(User::class))->toBe(['name'])
        ->and(FilamentWithdrawalRelations::isAllowed(User::class, 'password'))->toBeFalse();
});

it('rejects a class that is not allowlisted in the field editor', function () {
    livewire(WithdrawalMethodFieldsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->callTableAction('create', data: [
            'type' => 'select',
            'name' => 'beneficiary',
            'is_relation' => true,
            'relation_name' => User::class,
            'relation_column' => 'password',
        ])
        ->assertHasTableActionErrors(['relation_name']);

    expect($this->method->fields()->count())->toBe(0);
});

it('rejects a hidden column of an allowlisted model in the field editor', function () {
    config()->set('filament-withdrawals.relations', [User::class => ['name', 'password']]);

    livewire(WithdrawalMethodFieldsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->callTableAction('create', data: [
            'type' => 'select',
            'name' => 'beneficiary',
            'is_relation' => true,
            'relation_name' => User::class,
            'relation_column' => 'password',
        ])
        ->assertHasTableActionErrors(['relation_column']);

    expect($this->method->fields()->count())->toBe(0);
});

it('saves an allowlisted model and column from the field editor', function () {
    config()->set('filament-withdrawals.relations', [User::class => ['name']]);

    livewire(WithdrawalMethodFieldsRelation::class, [
        'ownerRecord' => $this->method,
        'pageClass' => EditWithdrawalMethod::class,
    ])
        ->callTableAction('create', data: [
            'type' => 'select',
            'name' => 'beneficiary',
            'is_relation' => true,
            'relation_name' => User::class,
            'relation_column' => 'name',
        ])
        ->assertHasNoTableActionErrors();

    $field = $this->method->fields()->first();

    expect($field->relation_name)->toBe(User::class)
        ->and($field->relation_column)->toBe('name');
});
