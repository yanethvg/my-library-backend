<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'author' => 'required|string',
            'genre_id' => 'exists:genres,id|required|integer',
            'year_published' => 'required|string|min:4|max:4',
            'stock' => 'required|integer|min:1'
        ];
    }
}
