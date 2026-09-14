<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use Illuminate\Support\Collection;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalFormFieldType;

class FilamentWithdrawalFormFields
{
    /**
     * Registered field types keyed by name, so registering a type again replaces it.
     *
     * @var array<string, WithdrawalFormFieldType>
     */
    public static array $formFields = [];

    /**
     * @param  WithdrawalFormFieldType|array<int, WithdrawalFormFieldType>  $field
     */
    public static function register(WithdrawalFormFieldType | array $field): void
    {
        if (is_array($field)) {
            foreach ($field as $type) {
                self::register($type);
            }

            return;
        }

        self::$formFields[$field->name] = $field;
    }

    /**
     * @return Collection<int, WithdrawalFormFieldType>
     */
    public static function getOptions(): Collection
    {
        return collect(array_values(self::$formFields));
    }
}
