<?php

use App\Models\User;

return [
    /*
     * Currency used to format amounts on the withdrawal screens.
     */
    'currency' => env('FILAMENT_WITHDRAWALS_CURRENCY', 'USD'),

    /*
     * Default owner model for requests created by the model factory.
     */
    'user_model' => User::class,

    /*
     * Optional accounts guard (tomatophp/filament-accounts). When a user is signed in on this guard,
     * form-builder requests are created for that account and checked against its wallet balance;
     * otherwise the default guard's user is used.
     */
    'accounts' => [
        'guard' => 'accounts',
    ],
];
