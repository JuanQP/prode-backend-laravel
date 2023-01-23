<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionCreateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'match' => 'required|integer',
            'team_a_score' => 'required|string|min:1|max:2',
            'team_b_score' => 'required|string|min:1|max:2',
        ];
    }
}
