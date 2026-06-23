<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\Enums\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the query parameters common to every paginated index endpoint.
 */
class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Upper bound is clamped (not rejected) by the controller; only
            // guard against nonsensical values here.
            'per_page' => ['integer', 'min:1'],
            'page' => ['integer', 'min:1'],
            'sort' => ['string'],
            'lang' => ['string', Rule::in(Language::values())],
        ];
    }
}
