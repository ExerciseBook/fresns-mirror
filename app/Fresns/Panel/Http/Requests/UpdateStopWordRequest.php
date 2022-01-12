<?php

namespace App\Fresns\Panel\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateStopWordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'word' => [
                'required',
                'string',
                Rule::unique('App\Models\StopWord')->ignore(optional($this->stopWord)->id),
            ],
            'replace_word' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
