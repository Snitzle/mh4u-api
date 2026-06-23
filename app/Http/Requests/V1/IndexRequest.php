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
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'sort' => ['string'],
            'lang' => ['string', Rule::in(Language::values())],
        ];
    }
}
