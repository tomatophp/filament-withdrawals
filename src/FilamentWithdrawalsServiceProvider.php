<?php

namespace TomatoPHP\FilamentWithdrawals;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalFormFieldType;
use TomatoPHP\FilamentWithdrawals\Services\FilamentWithdrawalFormFields;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use TomatoPHP\FilamentIcons\Components\IconPicker;

class FilamentWithdrawalsServiceProvider extends ServiceProvider
{
   public function register(): void
   {
      //Register generate command
      $this->commands([
         \TomatoPHP\FilamentWithdrawals\Console\FilamentWithdrawalsInstall::class,
      ]);


      //Register Migrations
      $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

      //Publish Migrations
      $this->publishes([
         __DIR__ . '/../database/migrations' => database_path('migrations'),
      ], 'filament-withdrawals-migrations');

      //Register Langs
      $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-withdrawals');

      //Publish Lang
      $this->publishes([
         __DIR__ . '/../resources/lang' => base_path('lang/vendor/filament-withdrawals'),
      ], 'filament-withdrawals-lang');

   }

   public function boot(): void
   {
      FilamentWithdrawalFormFields::register([
         WithdrawalFormFieldType::make('text')
            ->label('Text'),
         WithdrawalFormFieldType::make('textarea')
            ->className(Textarea::class)
            ->color('warning')
            ->icon('heroicon-s-document-text')
            ->label('Textarea'),
         WithdrawalFormFieldType::make('select')
            ->className(Select::class)
            ->color('info')
            ->icon('heroicon-s-squares-plus')
            ->label('Select'),
         WithdrawalFormFieldType::make('checkbox')
            ->className(Checkbox::class)
            ->color('danger')
            ->icon('heroicon-s-check')
            ->label('Checkbox'),
         WithdrawalFormFieldType::make('radio')
            ->className(Radio::class)
            ->color('success')
            ->icon('heroicon-s-check-circle')
            ->label('Radio'),
         WithdrawalFormFieldType::make('file')
            ->className(FileUpload::class)
            ->color('info')
            ->icon('heroicon-s-document-arrow-up')
            ->label('File'),
         WithdrawalFormFieldType::make('date')
            ->className(DatePicker::class)
            ->color('success')
            ->icon('heroicon-s-calendar')
            ->label('Date'),
         WithdrawalFormFieldType::make('time')
            ->className(TimePicker::class)
            ->color('info')
            ->icon('heroicon-s-clock')
            ->label('Time'),
         WithdrawalFormFieldType::make('datetime')
            ->className(DateTimePicker::class)
            ->color('warning')
            ->icon('heroicon-s-calendar-days')
            ->label('DateTime'),
         WithdrawalFormFieldType::make('color')
            ->className(ColorPicker::class)
            ->color('success')
            ->icon('heroicon-s-swatch')
            ->label('Color'),
         WithdrawalFormFieldType::make('icon')
            ->className(IconPicker::class)
            ->color('info')
            ->icon('heroicon-s-heart')
            ->label('Icon'),
         WithdrawalFormFieldType::make('toggle')
            ->className(Toggle::class)
            ->color('success')
            ->icon('heroicon-s-adjustments-horizontal')
            ->label('Toggle'),
         WithdrawalFormFieldType::make('password')
            ->color('danger')
            ->icon('heroicon-s-lock-closed')
            ->label('Password'),
         WithdrawalFormFieldType::make('email')
            ->color('info')
            ->icon('heroicon-s-envelope')
            ->label('Email'),
         WithdrawalFormFieldType::make('number')
            ->color('success')
            ->icon('heroicon-s-minus-circle')
            ->label('Number'),
         WithdrawalFormFieldType::make('url')
            ->color('primary')
            ->icon('heroicon-s-globe-alt')
            ->label('URL'),
         WithdrawalFormFieldType::make('tel')
            ->color('warning')
            ->icon('heroicon-s-phone')
            ->label('Tel'),
         WithdrawalFormFieldType::make('markdown')
            ->className(MarkdownEditor::class)
            ->color('warning')
            ->icon('heroicon-s-hashtag')
            ->label('Markdown'),
         WithdrawalFormFieldType::make('rich')
            ->className(RichEditor::class)
            ->color('info')
            ->icon('heroicon-s-document-text')
            ->label('RichText'),
         WithdrawalFormFieldType::make('keyValue')
            ->className(KeyValue::class)
            ->color('danger')
            ->icon('heroicon-s-key')
            ->label('Key/Value'),
         WithdrawalFormFieldType::make('repeater')
            ->className(Repeater::class)
            ->icon('heroicon-s-rectangle-group')
            ->label('Repeater'),
      ]);
   }
}
