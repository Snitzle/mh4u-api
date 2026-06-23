<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Monster extends BaseModel
{
    use Searchable;

    protected $table = 'monsters';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_hp' => 'integer',
            'hp_mult_low' => 'float',
            'hp_mult_high' => 'float',
            'hp_mult_g' => 'float',
            'crown_mini' => 'float',
            'crown_large' => 'float',
            'crown_king' => 'float',
            'rage_duration' => 'integer',
            'rage_mod_attack' => 'float',
            'rage_mod_defense' => 'float',
            'rage_mod_speed' => 'float',
            'limp_low' => 'integer',
            'limp_high' => 'integer',
            'limp_high_apex' => 'integer',
            'limp_g' => 'integer',
            'limp_g_apex' => 'integer',
            'cap_low' => 'integer',
            'cap_high' => 'integer',
            'cap_high_apex' => 'integer',
            'cap_g' => 'integer',
            'cap_g_apex' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'name_de' => $this->name_de,
            'name_fr' => $this->name_fr,
            'name_es' => $this->name_es,
            'name_it' => $this->name_it,
            'name_jp' => $this->name_jp,
        ];
    }

    /** @return HasMany<MonsterAilment, $this> */
    public function ailments(): HasMany
    {
        return $this->hasMany(MonsterAilment::class);
    }

    /** @return HasMany<MonsterDamage, $this> */
    public function damage(): HasMany
    {
        return $this->hasMany(MonsterDamage::class);
    }

    /** @return HasMany<MonsterWeakness, $this> */
    public function weaknesses(): HasMany
    {
        return $this->hasMany(MonsterWeakness::class);
    }

    /** @return HasMany<MonsterStatus, $this> */
    public function statuses(): HasMany
    {
        return $this->hasMany(MonsterStatus::class);
    }

    /** @return HasMany<MonsterHabitat, $this> */
    public function habitats(): HasMany
    {
        return $this->hasMany(MonsterHabitat::class);
    }

    /** @return HasMany<HuntingReward, $this> */
    public function huntingRewards(): HasMany
    {
        return $this->hasMany(HuntingReward::class);
    }

    /** @return BelongsToMany<Quest, $this> */
    public function quests(): BelongsToMany
    {
        return $this->belongsToMany(Quest::class, 'monster_quest')
            ->withPivot('unstable');
    }

    /** @return HasMany<MonsterStaggerLimit, $this> */
    public function staggerLimits(): HasMany
    {
        return $this->hasMany(MonsterStaggerLimit::class)->orderBy('sort_order');
    }

    /** @return HasMany<MonsterTrapEffect, $this> */
    public function trapEffects(): HasMany
    {
        return $this->hasMany(MonsterTrapEffect::class)->orderBy('sort_order');
    }

    /** @return HasMany<MonsterSound, $this> */
    public function sounds(): HasMany
    {
        return $this->hasMany(MonsterSound::class);
    }
}
