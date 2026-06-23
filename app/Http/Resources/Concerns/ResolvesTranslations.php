<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use App\Support\Translator;
use Illuminate\Database\Eloquent\Model;

trait ResolvesTranslations
{
    /**
     * Resolve a translatable attribute.
     *
     * With no `?lang` query parameter, returns an object keyed by language
     * code: {"en": "...", "de": "...", ...}. With `?lang=de`, returns the
     * single requested translation, falling back to English when blank.
     *
     * @return array<string, string|null>|string|null
     */
    protected function translate(Model $model, string $attribute): array|string|null
    {
        return Translator::resolve($model, $attribute);
    }
}
