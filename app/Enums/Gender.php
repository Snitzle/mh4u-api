<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Armor gender restriction. The empty string in the source data means
 * "no restriction" and is modelled as {@see Gender::Unspecified}.
 */
enum Gender: string
{
    use HasValues;

    case Unspecified = '';
    case Both = 'Both';
    case Male = 'Male';
    case Female = 'Female';
}
