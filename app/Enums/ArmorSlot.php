<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Armor body slot. The backing value matches `armor.slot` verbatim;
 * `iconPrefix()` maps it to the bundled icon filename stem.
 */
enum ArmorSlot: string
{
    use HasValues;

    case Head = 'Head';
    case Body = 'Body';
    case Arms = 'Arms';
    case Waist = 'Waist';
    case Legs = 'Legs';

    /**
     * Icon filename stem, e.g. "Head" => "head"
     * (matches icons_armor/icons_head/head{rarity}.png).
     */
    public function iconPrefix(): string
    {
        return strtolower($this->value);
    }
}
