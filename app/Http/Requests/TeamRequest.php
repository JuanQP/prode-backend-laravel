<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    public static $rules = [
        'name' => 'required|string|max:100',
        'short_name' => 'required|string|max:4',
        'image' => 'string',
    ];

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            return [
                'name' => 'sometimes|required|string|max:100',
                'short_name' => 'sometimes|required|string|max:4',
                'image' => 'sometimes|string',
            ];
        }
        return TeamRequest::$rules;
    }
}
