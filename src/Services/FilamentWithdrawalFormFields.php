<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use Illuminate\Support\Str;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalFormFieldType;

class FilamentWithdrawalFormFields
{
    public static array $formFields = [];

    public static function register(WithdrawalFormFieldType|array $field)
    {
        if (is_array($field)) {
            foreach ($field as $type) {
                self::register($type);
            }
            return;
        }
        self::$formFields[] = $field;
    }

    public static function getOptions()
    {
        return collect(self::$formFields);
    }
}
