<?php

namespace App\Enum;

enum Status: string
{
    case Available = 'available';
    case OutOfStock = 'outOfStock';
}
