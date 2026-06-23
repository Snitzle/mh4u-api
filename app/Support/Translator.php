<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Language;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves translatable model attributes (name, description) either as an
 * object keyed by language, or — when a `?lang` is requested — as a single
 * localized string with an English fallback.
 */
final class Translator
{
    /**
     * @return array<string, string|null>|string|null
     */
    public static function resolve(Model $model, string $attribute): array|string|null
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

    public static function requestedLanguage(): ?Language
    {
        $lang = request()->query('lang');

        return is_string($lang) ? Language::tryFrom($lang) : null;
    }
}
