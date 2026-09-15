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

    /*
     * Allowlist for relation fields (Select fields with "Is Relation" in a withdrawal method's fields).
     * Keys are model classes (or relationship names), values are the columns that may be shown as options.
     * Anything not listed renders the field without options and is never queried, and hidden model
     * attributes (password, remember_token, ...) are never shown even when listed. Empty by default.
     *
     * 'relations' => [
     *     \App\Models\Bank::class => ['name'],
     * ],
     */
    'relations' => [],
];
