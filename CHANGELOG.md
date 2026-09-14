### V5.0.0

- Support Filament v5 and Laravel 12 / 13 (requires tomatophp/filament-wallet ^5.0)
- Declare the real dependencies: media library plugin, spatie/laravel-translatable, filament-icons, filament-translation-component
- tomatophp/filament-accounts is optional: its account column is only used when it is installed
- Requests are created for the signed-in owner (accounts guard or default guard) instead of hard-coded `App\Models\Account` / `App\Models\User`
- New `config/filament-withdrawals.php` (currency, owner model, accounts guard)
- Fix payload labels on the request view (fields are matched by `name`), the final amount entry and the owner name column
- Fix form field types being registered again on every boot; re-registering a type now replaces it
- Field type guessing no longer marks names such as `rate` as dates
- Withdrawal method create page, model factories and a full Pest test suite
