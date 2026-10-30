<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function noteRules(): array
    {
        return [
            'text' => ['required', 'string'],
            'tag_id' => ['required', 'integer', 'exists:tags,id'],
        ];
    }

    public function rules(): array
    {
        return self::noteRules();
    }
}
