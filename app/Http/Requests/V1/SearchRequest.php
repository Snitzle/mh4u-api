<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\Enums\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
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
            'q' => ['required', 'string', 'min:1', 'max:100'],
            'types' => ['string'],
            'limit' => ['integer', 'min:1', 'max:50'],
            'lang' => ['string', Rule::in(Language::values())],
        ];
    }
}
