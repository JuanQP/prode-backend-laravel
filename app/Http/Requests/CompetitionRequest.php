<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompetitionRequest extends FormRequest
{
    public static $rules = [
        'name' => 'required|string|max:100',
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
            return Helpers::sometimes(CompetitionRequest::$rules);
        }
        return CompetitionRequest::$rules;
    }
}
