<?php

namespace App\Fresns\Panel\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEmojiGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rank_num' => 'string|required',
            'is_enable' => 'boolean|required',
            'code' => [
                'required',
                Rule::unique('App\Models\Emoji')->ignore($this->id),
            ],
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
