<?php

namespace App\Fresns\Panel\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEmojiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rule = [
            'rank_num' => 'string|required',
            'is_enable' => 'boolean|required',
        ];
        if ($this->method() == 'POST') {
            $rule['code'] = [
                'required',
                Rule::unique('App\Models\Emoji'),
            ];
        } elseif ($this->method() == 'PUT') {
            $rule['code'] = [
                'required',
                Rule::unique('App\Models\Emoji')->ignore($this->emoji->id),
            ];
        }
        return $rule;
    }

    public function attributes()
    {
        return [
        ];
    }
}
