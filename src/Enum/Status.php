<?php

namespace App\Enum;

enum Status: string
{
    case Full = 'full';
    case RunningOut = 'runningOut';
    case OutOfStock = 'outOfStock';

    public static function values() {
        $values = [];
        foreach (self::cases() as $status) {
            $values[] = $status->value;
        }

        return $values;
    }
}