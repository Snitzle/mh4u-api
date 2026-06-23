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

    /**
     * The class "modifier" (raw-damage divisor) used to derive true raw from a
     * weapon's displayed attack: true_raw = attack / modifier. Fixed per class.
     */
    public function modifier(): float
    {
        return match ($this) {
            self::GreatSword => 4.8,
            self::LongSword => 3.3,
            self::SwordAndShield => 1.4,
            self::DualBlades => 1.4,
            self::Hammer => 5.2,
            self::HuntingHorn => 4.2,
            self::Lance => 2.3,
            self::Gunlance => 2.3,
            self::SwitchAxe => 3.5,
            self::ChargeBlade => 3.6,
            self::InsectGlaive => 3.1,
            self::LightBowgun => 1.3,
            self::HeavyBowgun => 1.5,
            self::Bow => 1.2,
        };
    }
}
