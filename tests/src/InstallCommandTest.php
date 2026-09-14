<?php

use TomatoPHP\FilamentWithdrawals\Console\FilamentWithdrawalsInstall;

use function Pest\Laravel\artisan;

it('runs the install command', function () {
    // The real command shells out to `php artisan migrate`; skip that sub-process in tests.
    app()->bind(FilamentWithdrawalsInstall::class, fn () => new class extends FilamentWithdrawalsInstall
    {
        public array $ran = [];

        public function artisanCommand(array $command, ?bool $withOutput = false): void
        {
            $this->ran[] = $command;
        }
    });

    artisan('filament-withdrawals:install')
        ->expectsOutputToContain('Filament Withdrawals installed successfully.')
        ->assertSuccessful();
});
