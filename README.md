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

## Screenshots

![Withdrawal Methods](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-methods.png)
![Withdrawal Methods Fields](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-methods-fields.png)
![Withdrawal Methods Requests](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-methods-requests.png)
![Withdrawal Requests](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-requests.png)
![Withdrawal Requests View](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-requests-view.png)
![Withdrawal Requests Edit](https://raw.githubusercontent.com/tomatophp/filament-withdrawals/master/arts/withdrawal-requests-edit.png)

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


## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
