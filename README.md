# Filament Withdrawals

[![Latest Stable Version](https://poser.pugx.org/TomatoPHP/filament-withdrawals/version.svg)](https://packagist.org/packages/TomatoPHP/filament-withdrawals)
[![License](https://poser.pugx.org/TomatoPHP/filament-withdrawals/license.svg)](https://packagist.org/packages/TomatoPHP/filament-withdrawals)
[![Downloads](https://poser.pugx.org/TomatoPHP/filament-withdrawals/d/total.svg)](https://packagist.org/packages/TomatoPHP/filament-withdrawals)

Manage your withdrawals in Filament

## Installation

```bash
composer require tomatophp/filament-withdrawals
```
after install your package please run this command

```bash
php artisan filament-withdrawals:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentWithdrawals\FilamentWithdrawalsPlugin::make())
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Abdelmjid](https://wa.me/201091523908)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
