<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

use App\Enums\Language;
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
        $language = self::requestedLanguage();

        if ($language instanceof Language) {
            $value = $model->getAttribute($attribute.$language->columnSuffix());

            return filled($value) ? $value : $model->getAttribute($attribute);
        }

        $translations = [];

        foreach (Language::cases() as $candidate) {
            $translations[$candidate->value] = $model->getAttribute($attribute.$candidate->columnSuffix());
        }

        return $translations;
    }

    protected static function requestedLanguage(): ?Language
    {
        $lang = request()->query('lang');

        return is_string($lang) ? Language::tryFrom($lang) : null;
    }
}
