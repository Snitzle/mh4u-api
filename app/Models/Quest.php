<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuestHub;
use App\Enums\QuestType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

/**
 * @property QuestHub $hub
 * @property QuestType $type
 */
class Quest extends BaseModel
{
    use Searchable;

    protected $table = 'quests';

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    protected function casts(): array
    {
        return [
            'hub' => QuestHub::class,
            'type' => QuestType::class,
        ];
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return HasMany<QuestReward, $this> */
    public function rewards(): HasMany
    {
        return $this->hasMany(QuestReward::class);
    }

    /** @return BelongsToMany<Monster, $this> */
    public function monsters(): BelongsToMany
    {
        return $this->belongsToMany(Monster::class, 'monster_quest')
            ->withPivot('unstable');
    }

    /**
     * Quests that must be completed before this one becomes available.
     *
     * @return BelongsToMany<Quest, $this>
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'quest_prereqs', 'quest_id', 'prereq_id');
    }

    /**
     * Quests in which the given monster appears.
     *
     * @param  Builder<Quest>  $query
     */
    public function scopeForMonster(Builder $query, int|string $monsterId): void
    {
        $query->whereHas('monsters', fn ($monsters) => $monsters->whereKey($monsterId));
    }
}
