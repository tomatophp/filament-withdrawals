<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use Illuminate\Support\Str;
use TomatoPHP\FilamentWithdrawals\Services\Contracts\WithdrawalType;

class FilamentWithdrawalTypes
{
    public static array $withdrawalTypes = [];

    public static function register(WithdrawalType|array $withdrawalType)
    {
        if(is_array($withdrawalType)) {
            foreach($withdrawalType as $type) {
                self::register($type);
            }
            return;
        }

        self::$withdrawalTypes[] = $withdrawalType;
    }

    public static function getOptions()
    {
        return collect(self::$withdrawalTypes);
    }
}