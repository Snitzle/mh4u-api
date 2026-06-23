<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * The 14 MH4U weapon classes. The backing value matches `weapons.wtype`
 * verbatim; `iconPrefix()` maps it to the bundled icon filename stem.
 */
enum WeaponType: string
{
    use HasValues;

    case GreatSword = 'Great Sword';
    case LongSword = 'Long Sword';
    case SwordAndShield = 'Sword and Shield';
    case DualBlades = 'Dual Blades';
    case Hammer = 'Hammer';
    case HuntingHorn = 'Hunting Horn';
    case Lance = 'Lance';
    case Gunlance = 'Gunlance';
    case SwitchAxe = 'Switch Axe';
    case ChargeBlade = 'Charge Blade';
    case InsectGlaive = 'Insect Glaive';
    case LightBowgun = 'Light Bowgun';
    case HeavyBowgun = 'Heavy Bowgun';
    case Bow = 'Bow';

    /**
     * Icon filename stem, e.g. "Sword and Shield" => "sword_and_shield"
     * (matches icons_weapons/icons_sword_and_shield/sword_and_shield{rarity}.png).
     */
    public function iconPrefix(): string
    {
        return str_replace(' ', '_', strtolower($this->value));
    }

    public function isBowgun(): bool
    {
        return $this === self::LightBowgun || $this === self::HeavyBowgun;
    }

    public function isRanged(): bool
    {
        return $this->isBowgun() || $this === self::Bow;
    }

    public function isHuntingHorn(): bool
    {
        return $this === self::HuntingHorn;
    }
}
