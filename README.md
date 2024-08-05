![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/megoxv-tomato-withdrawals.jpg)

# Filament Withdrawals

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-withdrawals/version.svg)](https://packagist.org/packages/tomatophp/filament-withdrawals)
[![License](https://poser.pugx.org/tomatophp/filament-withdrawals/license.svg)](https://packagist.org/packages/tomatophp/filament-withdrawals)
[![Downloads](https://poser.pugx.org/tomatophp/filament-withdrawals/d/total.svg)](https://packagist.org/packages/tomatophp/filament-withdrawals)

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

## Add Form Field Type

you can add more fields to the form builder by use this method on your provider.

```php
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormFields;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalFormFieldType;

FilamentWithdrawalFormFields::register([
    WithdrawalFormFieldType::make('code')
        ->className(CodeEditor::class)
        ->color('warning')
        ->icon('heroicon-s-code-bracket-square')
        ->label('Code Editor'),
]);
```

## Use Your Form Builder

after create your form you can use it by `id` like this

```php
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;

FilamentWithdrawalFormBuilder::make(1)->build()
```

## Use Form Requests to Submit your form data

you can use form requests to submit your form data by use this method on your provider.

```php
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormBuilder;

FilamentWithdrawalFormBuilder::make(1)->send($data)
```

## Publish Assets

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-withdrawals-migrations"
```


## Support

you can join our discord server to get support [TomatoPHP](https://discord.gg/vKV9U7gD3c)

## Docs

you can check docs of this package on [Docs](https://docs.tomatophp.com/filament/filament-withdrawals)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Abdelmjid](https://wa.me/201091523908)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
