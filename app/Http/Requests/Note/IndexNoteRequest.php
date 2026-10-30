<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class IndexNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag_id' => ['sometimes', 'integer', 'exists:tags,id'],
        ];
    }
}
