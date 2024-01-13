<?php

namespace App\Enum;

enum Status: string
{
    case Full = 'full';
    case RunningOut = 'running out';
    case OutOfStock = 'out of stock';
}