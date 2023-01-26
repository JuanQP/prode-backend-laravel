<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatchRequest extends FormRequest
{
    public static $rules = [
        'competition' => 'required|numeric',
        'team_a' => 'required|numeric',
        'team_b' => 'required|numeric|different:team_a',
        'datetime' => 'required|date_format:Y-m-d\\TH:i',
        'stadium' => 'required|string|max:50',
        'description' => 'required|string|max:100',
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
            return array_diff_key(
                Helpers::sometimes(MatchRequest::$rules),
                ['competition' => false],
            );
        }
        return MatchRequest::$rules;
    }
}
