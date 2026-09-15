### V5.0.1

- **Security fix:** relation fields of withdrawal forms loaded any class and any column typed into the field editor (for example `App\Models\User` + `password` put the password hashes of every user into the request form, and any class name got `::all()` called on it). Relation fields now only read the models / relationships and columns listed in the new `relations` allowlist of `config/filament-withdrawals.php`, hidden model attributes (password, remember_token, ...) are never shown even when listed, and the field editor offers the allowlisted models and columns as validated selects.
- **Upgrade note:** the allowlist is empty by default. Apps that use relation fields must add each model (or relationship) and column to `filament-withdrawals.relations`; until then those fields render without options.

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
