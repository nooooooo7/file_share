<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolderMetaDataFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'by' => 'sometimes|in:id,created_at,updated_at',
            'order' =>  'sometimes|in:ASC,DESC',
            'per_page' => 'sometimes|min:1|max:50',
        ];
    }
}
